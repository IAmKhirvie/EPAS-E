<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\InformationSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\SanitizesContent;

class InformationSheetController extends Controller
{
    use SanitizesContent;
    public function create(Module $module)
    {
        $nextOrder = $module->informationSheets()->max('order') + 1;
        return view('modules.information-sheets.create', compact('module', 'nextOrder'));
    }

    public function store(Request $request, Module $module)
    {
        $validated = $request->validate([
            'sheet_number' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'order' => 'required|integer|min:0',
        ]);

        try {
            // Sanitize content to prevent XSS
            $validated = $this->sanitizeFields($validated, ['content']);

            $informationSheet = $module->informationSheets()->create($validated);

            return redirect()->route('courses.index')
                ->with('success', "Information Sheet {$informationSheet->sheet_number} created successfully!");

        } catch (\Exception $e) {
            Log::error('Information sheet creation failed: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Failed to create information sheet. Please try again.');
        }
    }

    public function edit(Module $module, InformationSheet $informationSheet)
    {
        return view('modules.information-sheets.edit', compact('module', 'informationSheet'));
    }

    public function update(Request $request, Module $module, InformationSheet $informationSheet)
    {
        $validated = $request->validate([
            'sheet_number' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'order' => 'required|integer|min:0',
        ]);

        try {
            // Sanitize content to prevent XSS
            $validated = $this->sanitizeFields($validated, ['content']);

            $informationSheet->update($validated);

            return redirect()->route('courses.index')
                ->with('success', "Information Sheet {$informationSheet->sheet_number} updated successfully!");

        } catch (\Exception $e) {
            Log::error('Information sheet update failed: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Failed to update information sheet. Please try again.');
        }
    }

    public function destroy(Module $module, InformationSheet $informationSheet)
    {
        try {
            $sheetNumber = $informationSheet->sheet_number;
            $informationSheet->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'message' => "Information Sheet {$sheetNumber} deleted successfully!"
                ]);
            }

            return redirect()->route('courses.index')
                ->with('success', "Information Sheet {$sheetNumber} deleted successfully!");

        } catch (\Exception $e) {
            Log::error('Information sheet deletion failed: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to delete information sheet. Please try again.'
                ], 500);
            }
            
            return back()->with('error', 'Failed to delete information sheet. Please try again.');
        }
    }
}