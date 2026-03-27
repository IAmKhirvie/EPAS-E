document.addEventListener('DOMContentLoaded', function () {
    const moduleData = document.getElementById('moduleData');
    const csrfToken = moduleData.dataset.csrf;
    const baseUrl = moduleData.dataset.baseUrl;
    const moduleId = moduleData.dataset.moduleId;
    const courseId = moduleData.dataset.courseId;

    // ==================== TOC NAVIGATION ====================

    // Dropdown toggle buttons - expand/collapse topics
    document.querySelectorAll('.sidebar-dropdown-toggle').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const sheetItem = this.closest('.sidebar-sheet-item');
            const wasExpanded = sheetItem.classList.contains('expanded');

            // Collapse all
            document.querySelectorAll('.sidebar-sheet-item').forEach(function (item) {
                item.classList.remove('expanded');
            });

            // Toggle this one
            if (!wasExpanded) {
                sheetItem.classList.add('expanded');
            }
        });
    });

    // Sheet headers - show "Start Reading" view
    document.querySelectorAll('.sidebar-sheet-header').forEach(function (header) {
        header.addEventListener('click', function (e) {
            e.preventDefault();
            const sheetId = this.dataset.sheetId;
            const sheetIndex = parseInt(this.dataset.sheetIndex) || 0;
            showSheetStartReading(sheetId, sheetIndex, this);
        });
    });

    // Topic items - enter focus mode at specific topic
    document.querySelectorAll('.sidebar-topic-item:not(a)').forEach(function (item) {
        item.addEventListener('click', function () {
            const topicId = this.dataset.topicId;
            const sheetId = this.dataset.sheetId;

            if (topicId) {
                // Find the index in focusModeData and enter focus mode
                enterFocusModeAtTopic(sheetId, topicId);
            }

            setActiveItem(this);
            closeMobileToc();
        });
    });

    // Overview link
    const overviewLink = document.querySelector('[data-section="overview"]');
    if (overviewLink) {
        overviewLink.addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('overviewSection').style.display = 'block';
            document.getElementById('dynamicContent').style.display = 'none';
            setActiveItem(this);
            closeMobileToc();
        });
    }

    function setActiveItem(element) {
        document.querySelectorAll('.sidebar-toc-link').forEach(function (l) { l.classList.remove('active'); });
        document.querySelectorAll('.sidebar-topic-item').forEach(function (l) { l.classList.remove('active'); });
        document.querySelectorAll('.sidebar-sheet-header').forEach(function (l) { l.classList.remove('active'); });
        element.classList.add('active');
    }

    // ==================== SHEET "START READING" VIEW ====================

    function showSheetStartReading(sheetId, sheetIndex, headerElement) {
        const contentArea = document.getElementById('dynamicContent');
        const overviewSection = document.getElementById('overviewSection');

        overviewSection.style.display = 'none';
        contentArea.style.display = 'block';

        // Get sheet info from the header
        const sheetItem = headerElement.closest('.sidebar-sheet-item');
        const sheetTitle = sheetItem.querySelector('.sidebar-sheet-main').textContent;
        const sheetSub = sheetItem.querySelector('.sidebar-sheet-sub').textContent;

        // Count topics
        const topicItems = sheetItem.querySelectorAll('.sidebar-topic-item[data-topic-id]');
        const topicCount = topicItems.length;

        contentArea.innerHTML = `
            <div class="start-reading-card">
                <div class="start-reading-icon">
                    <i class="fas fa-book-reader"></i>
                </div>
                <h2 class="start-reading-title">${sheetTitle}</h2>
                <p class="start-reading-meta">${sheetSub}</p>
                <div class="start-reading-info">
                    <div class="info-item">
                        <i class="fas fa-file-alt"></i>
                        <span>${topicCount} Topics</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <span>~${Math.ceil(topicCount * 3)} min read</span>
                    </div>
                </div>
                <button class="btn btn-category btn-lg start-reading-btn" data-sheet-id="${sheetId}" data-sheet-index="${sheetIndex}">
                    <i class="fas fa-play me-2"></i> Start Reading
                </button>
                <p class="start-reading-hint">
                    <i class="fas fa-info-circle me-1"></i>
                    Opens in Focus Mode for distraction-free reading
                </p>
            </div>
        `;

        // Bind the start reading button
        contentArea.querySelector('.start-reading-btn').addEventListener('click', function () {
            const sId = this.dataset.sheetId;
            enterFocusModeAtSheet(sId);
        });

        setActiveItem(headerElement);
    }

    // ==================== MOBILE TOC ====================

    const tocToggle = document.getElementById('tocMobileToggle');
    const sidebar = document.querySelector('.sidebar-section');

    if (tocToggle && sidebar) {
        const overlay = document.createElement('div');
        overlay.className = 'toc-mobile-overlay';
        document.body.appendChild(overlay);

        tocToggle.addEventListener('click', function () {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', closeMobileToc);
    }

    function closeMobileToc() {
        if (sidebar) sidebar.classList.remove('mobile-open');
        const overlay = document.querySelector('.toc-mobile-overlay');
        if (overlay) overlay.classList.remove('active');
    }

    // ==================== PROGRESS ====================

    function fetchProgress() {
        fetch(`/courses/${courseId}/module-${moduleId}/progress`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
        })
        .then(r => r.json())
        .then(data => {
            updateProgressDisplay(data);
        })
        .catch(() => {});
    }

    fetchProgress();

    function updateProgressDisplay(progress) {
        const percentage = progress.percentage || 0;

        // Update progress circle
        const circle = document.getElementById('progressCircle');
        if (circle) {
            const circumference = 2 * Math.PI * 40;
            const offset = circumference - (percentage / 100) * circumference;
            circle.style.strokeDasharray = circumference;
            circle.style.strokeDashoffset = offset;
        }

        // Update progress text
        const progressText = document.getElementById('progressText');
        if (progressText) progressText.textContent = Math.round(percentage) + '%';

        // Update progress badge
        const progressBadge = document.getElementById('progressBadge');
        if (progressBadge) progressBadge.textContent = Math.round(percentage) + '%';

        // Update completed count (show "X of Y" format)
        const completedCount = document.getElementById('completedCount');
        if (completedCount) {
            const completed = progress.completed_items !== undefined ? progress.completed_items : (progress.completed || 0);
            const total = progress.total_items !== undefined ? progress.total_items : (progress.total || 0);
            completedCount.textContent = completed + ' of ' + total;
        }
    }

    // ==================== FOCUS MODE ====================

    const focusModeContainer = document.getElementById('focusModeContainer');
    const focusModeDataEl = document.getElementById('focusModeData');
    let focusModeData = [];
    let currentFocusIndex = 0;
    let currentImageIndex = 0;

    // Track completed sections
    let completedSections = new Set();
    const storageKey = 'focus_completed_' + moduleId;
    try {
        const saved = localStorage.getItem(storageKey);
        if (saved) completedSections = new Set(JSON.parse(saved));
    } catch (e) {}

    // Track if current section has been scrolled to bottom
    let currentSectionScrolled = false;
    let scrollHandler = null;

    try {
        focusModeData = JSON.parse(focusModeDataEl.textContent);
    } catch (e) {
        console.error('Failed to parse focus mode data:', e);
    }

    function enterFocusMode() {
        currentFocusIndex = 0;
        startFocusMode();
    }

    function enterFocusModeAtSheet(sheetId) {
        // Find first topic of this sheet
        for (let i = 0; i < focusModeData.length; i++) {
            if (focusModeData[i].sheetId == sheetId && focusModeData[i].type === 'topic') {
                currentFocusIndex = i;
                break;
            }
        }
        startFocusMode();
    }

    function enterFocusModeAtTopic(sheetId, topicId) {
        // Find this specific topic
        for (let i = 0; i < focusModeData.length; i++) {
            if (focusModeData[i].id == topicId && focusModeData[i].type === 'topic') {
                currentFocusIndex = i;
                break;
            }
        }
        startFocusMode();
    }

    function startFocusMode() {
        document.body.classList.add('focus-mode-active');
        focusModeContainer.classList.add('active');
        updateFocusContent();
        document.addEventListener('keydown', focusKeyHandler);
        setupScrollTracking();
    }

    function exitFocusMode() {
        document.body.classList.remove('focus-mode-active');
        focusModeContainer.classList.remove('active');
        document.removeEventListener('keydown', focusKeyHandler);
        removeScrollTracking();
        // Refresh progress when exiting
        fetchProgress();
    }

    function focusKeyHandler(e) {
        if (e.key === 'Escape') exitFocusMode();
        else if (e.key === 'ArrowRight' || e.key === ' ') {
            e.preventDefault();
            if (canProceedToNext()) nextFocusContent();
        }
        else if (e.key === 'ArrowLeft') { e.preventDefault(); prevFocusContent(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); prevImage(); }
        else if (e.key === 'ArrowDown') { e.preventDefault(); nextImage(); }
    }

    function canProceedToNext() {
        return completedSections.has(currentFocusIndex) || currentSectionScrolled;
    }

    function setupScrollTracking() {
        removeScrollTracking();
        const contentPanel = document.querySelector('.focus-content-panel');
        if (!contentPanel) return;

        scrollHandler = function() {
            checkScrollCompletion(contentPanel);
        };
        contentPanel.addEventListener('scroll', scrollHandler);
    }

    function removeScrollTracking() {
        if (scrollHandler) {
            const contentPanel = document.querySelector('.focus-content-panel');
            if (contentPanel) {
                contentPanel.removeEventListener('scroll', scrollHandler);
            }
            scrollHandler = null;
        }
    }

    function checkScrollCompletion(panel) {
        const scrolledToBottom = panel.scrollHeight - panel.scrollTop - panel.clientHeight < 50;

        if (scrolledToBottom && !currentSectionScrolled) {
            currentSectionScrolled = true;
            markCurrentSectionComplete();
        }

        updateNextButtonState();
    }

    function markCurrentSectionComplete() {
        completedSections.add(currentFocusIndex);

        // Save to localStorage
        try {
            localStorage.setItem(storageKey, JSON.stringify([...completedSections]));
        } catch (e) {}

        // If this is a topic, call the API to record progress
        const content = focusModeData[currentFocusIndex];
        if (content && content.type === 'topic' && content.id && content.sheetId) {
            markTopicCompleteOnServer(content.sheetId, content.id);
        }

        updateNextButtonState();
    }

    function markTopicCompleteOnServer(sheetId, topicId) {
        console.log('Marking topic complete:', sheetId, topicId);

        fetch(`${baseUrl}/sheets/${sheetId}/topics/${topicId}/complete`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            console.log('Topic complete response:', data);
            if (data.success && data.progress) {
                updateProgressDisplay(data.progress);
            }
        })
        .catch(err => console.error('Failed to mark topic complete:', err));
    }

    function updateNextButtonState() {
        const nextBtn = document.getElementById('focusNextBtn');
        if (!nextBtn) return;

        const isLastSection = currentFocusIndex >= focusModeData.length - 1;
        const canProceed = canProceedToNext();

        nextBtn.disabled = isLastSection || !canProceed;

        if (!isLastSection && !canProceed) {
            nextBtn.innerHTML = '<i class="fas fa-lock me-2"></i>Scroll to Continue';
            nextBtn.classList.add('btn-locked');
        } else if (isLastSection) {
            nextBtn.innerHTML = '<i class="fas fa-check me-2"></i>Complete';
            nextBtn.classList.remove('btn-locked');
        } else {
            nextBtn.innerHTML = 'Next<i class="fas fa-arrow-right ms-2"></i>';
            nextBtn.classList.remove('btn-locked');
        }
    }

    function updateFocusContent() {
        const content = focusModeData[currentFocusIndex];
        if (!content) return;

        // Reset scroll state for new section (unless already completed)
        currentSectionScrolled = completedSections.has(currentFocusIndex);

        document.getElementById('focusModeTitle').textContent = content.title;
        document.getElementById('focusContentTitle').textContent = content.title;

        // Build content and extract tables
        let bodyHtml = '';
        let extractedTables = [];

        if (content.content) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = content.content;

            const tables = tempDiv.querySelectorAll('table');
            tables.forEach(function(table) {
                extractedTables.push(table.outerHTML);
                table.remove();
            });

            bodyHtml += '<div class="mb-4">' + tempDiv.innerHTML.replace(/\n/g, '<br>') + '</div>';
        }

        if (content.parts && content.parts.length > 0) {
            content.parts.forEach(function (part, idx) {
                bodyHtml += '<div class="part-section mb-4 p-3 bg-light rounded">' +
                    '<h5><span class="badge bg-primary me-2">' + (idx + 1) + '</span>' + (part.title || '') + '</h5>' +
                    '<p>' + (part.explanation || '').replace(/\n/g, '<br>') + '</p>' +
                    '</div>';
            });
        }

        // Add scroll indicator at the bottom
        bodyHtml += '<div class="scroll-end-marker" id="scrollEndMarker">' +
            '<i class="fas fa-check-circle"></i> End of section' +
            '</div>';

        document.getElementById('focusContentBody').innerHTML = bodyHtml || '<p class="text-muted">No content available for this section.</p>';

        // Scroll content panel to top and check if scrolling is needed
        const contentPanel = document.querySelector('.focus-content-panel');
        if (contentPanel) {
            contentPanel.scrollTop = 0;

            // Check if content fits without scrolling
            setTimeout(() => {
                if (contentPanel.scrollHeight <= contentPanel.clientHeight + 50) {
                    currentSectionScrolled = true;
                    markCurrentSectionComplete();
                }
                updateNextButtonState();
            }, 100);
        }

        currentImageIndex = 0;
        updateFocusImageAndTables(content, extractedTables);

        document.getElementById('focusProgressBadge').textContent = (currentFocusIndex + 1) + ' / ' + focusModeData.length;
        document.getElementById('focusPrevBtn').disabled = currentFocusIndex === 0;
        updateNextButtonState();

        // Re-setup scroll tracking for new content
        setupScrollTracking();
    }

    function updateFocusImage(content) {
        updateFocusImageAndTables(content, []);
    }

    function updateFocusImageAndTables(content, extractedTables) {
        const images = content.images || [];
        const noImage = document.getElementById('focusNoImage');
        const focusImage = document.getElementById('focusImage');
        const imageCaption = document.getElementById('focusImageCaption');
        const imageNav = document.getElementById('imageNav');
        const imageCounter = document.getElementById('imageCounter');
        const focusModeBody = document.querySelector('.focus-mode-body');

        focusModeBody.classList.remove('no-images', 'has-tables');

        const hasImages = images.length > 0;
        const hasTables = extractedTables && extractedTables.length > 0;

        if (!hasImages && !hasTables) {
            focusModeBody.classList.add('no-images');
            noImage.style.display = 'none';
            focusImage.style.display = 'none';
            imageNav.style.display = 'none';
            imageCaption.textContent = '';
            imageCounter.textContent = '';
        } else if (hasTables && !hasImages) {
            focusModeBody.classList.add('has-tables');
            noImage.style.display = 'none';
            focusImage.style.display = 'none';
            imageNav.style.display = 'none';

            let tableHtml = '<div class="table-label"><i class="fas fa-table me-2"></i>Reference Table</div>';
            extractedTables.forEach(function(table) {
                tableHtml += '<div class="table-container">' + table + '</div>';
            });
            imageCaption.innerHTML = tableHtml;
            imageCounter.textContent = extractedTables.length > 1 ? extractedTables.length + ' tables' : '';
        } else if (hasImages) {
            noImage.style.display = 'none';
            focusImage.style.display = 'block';
            const img = images[currentImageIndex];
            focusImage.src = typeof img === 'string' ? img : (img.url || img);
            imageCaption.textContent = typeof img === 'object' ? (img.caption || '') : '';

            if (images.length > 1) {
                imageNav.style.display = 'flex';
                imageCounter.textContent = 'Image ' + (currentImageIndex + 1) + ' of ' + images.length;
            } else {
                imageNav.style.display = 'none';
                imageCounter.textContent = '';
            }
        }
    }

    function nextFocusContent() {
        if (currentFocusIndex < focusModeData.length - 1 && canProceedToNext()) {
            currentFocusIndex++;
            updateFocusContent();
        } else if (currentFocusIndex >= focusModeData.length - 1) {
            // Last section completed - show completion message
            showNotification('Section completed! Great work!', 'success');
        }
    }

    function prevFocusContent() {
        if (currentFocusIndex > 0) {
            currentFocusIndex--;
            updateFocusContent();
        }
    }

    function nextImage() {
        const content = focusModeData[currentFocusIndex];
        if (currentImageIndex < (content.images || []).length - 1) {
            currentImageIndex++;
            updateFocusImage(content);
        }
    }

    function prevImage() {
        if (currentImageIndex > 0) {
            currentImageIndex--;
            updateFocusImage(focusModeData[currentFocusIndex]);
        }
    }

    // Focus mode event listeners
    document.getElementById('enterFocusMode').addEventListener('click', enterFocusMode);
    document.getElementById('focusModeFloatingBtn').addEventListener('click', enterFocusMode);
    document.getElementById('exitFocusMode').addEventListener('click', exitFocusMode);
    document.getElementById('focusPrevBtn').addEventListener('click', prevFocusContent);
    document.getElementById('focusNextBtn').addEventListener('click', nextFocusContent);
    document.getElementById('prevImage').addEventListener('click', prevImage);
    document.getElementById('nextImage').addEventListener('click', nextImage);

    // ==================== OFFLINE SAVE ====================

    const saveOfflineBtn = document.getElementById('saveOfflineBtn');

    saveOfflineBtn.addEventListener('click', function () {
        saveOfflineBtn.disabled = true;
        saveOfflineBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Generating...';

        try {
            const moduleTitle = document.querySelector('.module-header-section h4')?.textContent || 'Module';
            const offlineHtml = generateOfflineFocusModeHtml(focusModeData, moduleTitle);

            const blob = new Blob([offlineHtml], { type: 'text/html' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = moduleTitle.replace(/[^a-zA-Z0-9]/g, '-').toLowerCase() + '-offline.html';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);

            saveOfflineBtn.innerHTML = '<i class="fas fa-check me-1"></i> Downloaded';
            saveOfflineBtn.classList.remove('btn-outline-warning');
            saveOfflineBtn.classList.add('btn-success');
            showNotification('Focus mode version downloaded!', 'success');
        } catch (error) {
            console.error('Offline save error:', error);
            saveOfflineBtn.innerHTML = '<i class="fas fa-cloud-download-alt me-1"></i> Save Offline';
            showNotification('Failed to generate offline version.', 'error');
        } finally {
            saveOfflineBtn.disabled = false;
        }
    });

    function generateOfflineFocusModeHtml(data, title) {
        if (!data || !Array.isArray(data) || data.length === 0) {
            return '<!DOCTYPE html><html><head><title>No Content</title></head><body><h1>No content available</h1></body></html>';
        }

        function safeText(text) {
            if (!text) return '';
            return String(text);
        }

        let contentHtml = '';
        data.forEach(function(item, index) {
            if (!item) return;

            let itemContent = '';
            if (item.content) {
                itemContent += '<div class="content-text">' + safeText(item.content).replace(/\n/g, '<br>') + '</div>';
            }
            if (item.parts && Array.isArray(item.parts) && item.parts.length > 0) {
                item.parts.forEach(function(part, pIdx) {
                    if (!part) return;
                    itemContent += '<div class="part-section">' +
                        '<h4><span class="part-badge">' + (pIdx + 1) + '</span> ' + safeText(part.title) + '</h4>' +
                        '<p>' + safeText(part.explanation).replace(/\n/g, '<br>') + '</p>' +
                        '</div>';
                });
            }

            contentHtml += '<section class="content-section" id="section-' + index + '">' +
                '<h2>' + safeText(item.title || 'Section ' + (index + 1)) + '</h2>' +
                itemContent +
                '</section>';
        });

        let navHtml = '<nav class="offline-nav"><ul>';
        data.forEach(function(item, index) {
            if (!item) return;
            navHtml += '<li><a href="#section-' + index + '">' + safeText(item.title || 'Section ' + (index + 1)) + '</a></li>';
        });
        navHtml += '</ul></nav>';

        const isDarkMode = document.body.classList.contains('dark-mode');

        return '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">' +
            '<title>' + title + ' - Offline</title><style>' +
            'body{font-family:-apple-system,sans-serif;margin:0;' + (isDarkMode ? 'background:#121220;color:#e9ecef;' : 'background:#f8f9fa;color:#333;') + '}' +
            '.header{background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;padding:20px 30px;position:sticky;top:0;z-index:100}' +
            '.header h1{margin:0;font-size:1.5rem}.container{display:flex;max-width:1400px;margin:0 auto}' +
            '.offline-nav{width:280px;padding:20px;position:sticky;top:80px;height:calc(100vh - 80px);overflow-y:auto;' + (isDarkMode ? 'background:#1a1a2e;' : 'background:#fff;') + 'border-right:1px solid ' + (isDarkMode ? '#3a3a5a' : '#dee2e6') + '}' +
            '.offline-nav ul{list-style:none;padding:0;margin:0}.offline-nav li{margin-bottom:8px}' +
            '.offline-nav a{display:block;padding:10px 15px;text-decoration:none;border-radius:8px;' + (isDarkMode ? 'color:#adb5bd;background:#2a2a3e;' : 'color:#333;background:#f8f9fa;') + '}' +
            '.offline-nav a:hover{background:#667eea;color:#fff}.main-content{flex:1;padding:30px 50px;max-width:900px}' +
            '.content-section{margin-bottom:60px;padding-bottom:40px;border-bottom:2px solid ' + (isDarkMode ? '#3a3a5a' : '#e9ecef') + '}' +
            '.content-section h2{color:#667eea;border-bottom:3px solid #667eea;padding-bottom:15px;margin-bottom:25px}' +
            '.part-section{padding:20px;margin:20px 0;border-radius:8px;border-left:4px solid #667eea;' + (isDarkMode ? 'background:#2a2a3e;' : 'background:#f8f9fa;') + '}' +
            '.part-badge{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;background:#667eea;color:#fff;border-radius:50%;font-size:.85rem;margin-right:10px}' +
            '@media(max-width:768px){.container{flex-direction:column}.offline-nav{width:100%;position:static;height:auto}.main-content{padding:20px}}' +
            '</style></head><body><header class="header"><h1>' + title + '</h1></header>' +
            '<div class="container">' + navHtml + '<main class="main-content">' + contentHtml +
            '<div style="text-align:center;padding:40px;color:#888"><p>— End of Module —</p></div></main></div></body></html>';
    }

    // ==================== NOTIFICATIONS ====================

    function showNotification(message, type) {
        const toast = document.createElement('div');
        toast.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger') + ' position-fixed';
        toast.style.cssText = 'bottom: 20px; right: 20px; z-index: 9999; animation: fadeIn 0.3s;';
        toast.innerHTML = '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + ' me-2"></i>' + message;
        document.body.appendChild(toast);

        setTimeout(function () {
            toast.style.animation = 'fadeOut 0.3s';
            setTimeout(function () { toast.remove(); }, 300);
        }, 3000);
    }
});
