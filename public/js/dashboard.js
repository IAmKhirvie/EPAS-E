// public/js/dashboard.js
document.addEventListener('DOMContentLoaded', function() {
    const userRole = document.body.getAttribute('data-user-role')?.toLowerCase();
    
    if (userRole === 'admin' || userRole === 'instructor') {
        // Data is already loaded via PHP for admin/instructor
        console.log('Dashboard data loaded');
    } else {
        loadStudentDashboard();
    }
});

async function loadStudentDashboard() {
    try {
        const response = await fetch('/student/dashboard-data');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        updateStudentDashboard(data);
    } catch (error) {
        console.error('Error loading student dashboard:', error);
        // Fallback to server-side rendered data or mock data
        const serverData = {
            progress: document.getElementById('student-progress-text')?.getAttribute('data-progress') || 0,
            finished_activities: document.getElementById('finished-activities')?.getAttribute('data-activities') || '0/0',
            total_modules: document.getElementById('total-modules-count')?.getAttribute('data-modules') || 0,
            average_grade: document.getElementById('average-grade')?.getAttribute('data-grade') || '0%'
        };
        updateStudentDashboard(serverData);
    }
}

function updateStudentDashboard(data) {
    // Update progress circle
    const progressText = document.getElementById('student-progress-text');
    if (progressText) {
        progressText.textContent = data.progress + '%';
        
        // Update progress circle visual (if you have CSS for this)
        const progressCircle = progressText.closest('.progress-circle');
        if (progressCircle) {
            progressCircle.style.setProperty('--progress', data.progress + '%');
        }
    }

    // Update finished activities
    const finishedActivities = document.getElementById('finished-activities');
    if (finishedActivities) {
        finishedActivities.textContent = data.finished_activities;
    }

    // Update total modules
    const totalModulesCount = document.getElementById('total-modules-count');
    if (totalModulesCount) {
        totalModulesCount.textContent = data.total_modules;
    }

    // Update average grade
    const averageGrade = document.getElementById('average-grade');
    if (averageGrade) {
        // Handle both number and string formats
        let gradeValue = data.average_grade;
        if (typeof gradeValue === 'number') {
            averageGrade.textContent = gradeValue.toFixed(1) + '%';
        } else {
            averageGrade.textContent = gradeValue.toString().includes('%') ? gradeValue : gradeValue + '%';
        }
    }
}

// Add this function to handle progress circle animation
function initializeProgressCircles() {
    const progressElements = document.querySelectorAll('.progress-text');
    progressElements.forEach(element => {
        const progress = parseInt(element.textContent) || 0;
        const circle = element.closest('.progress-circle');
        if (circle) {
            circle.style.setProperty('--progress', progress + '%');
        }
    });
}

// Call this on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeProgressCircles();
});