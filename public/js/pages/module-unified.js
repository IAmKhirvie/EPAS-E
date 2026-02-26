document.addEventListener('DOMContentLoaded', function () {
    const moduleData = document.getElementById('moduleData');
    const csrfToken = moduleData.dataset.csrf;
    const baseUrl = moduleData.dataset.baseUrl;

    // ==================== TOC NAVIGATION ====================

    // Sheet links - toggle subitems and load content
    document.querySelectorAll('.sheet-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const sheetId = this.dataset.sheetId;
            const subitems = this.nextElementSibling;
            const icon = this.querySelector('.toggle-icon');

            if (subitems) {
                const isVisible = subitems.style.display !== 'none';
                subitems.style.display = isVisible ? 'none' : 'block';
                icon.classList.toggle('fa-chevron-down', isVisible);
                icon.classList.toggle('fa-chevron-up', !isVisible);
            }

            loadSheetContent(sheetId);
            setActiveLink(this);
        });
    });

    // Sub-links: content, topics, assessments
    document.querySelectorAll('.toc-sublink').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const sheetId = this.dataset.sheetId;
            const topicId = this.dataset.topicId;
            const assessment = this.dataset.assessment;

            if (assessment) {
                loadAssessmentContent(sheetId, assessment);
            } else if (topicId) {
                loadTopicContent(sheetId, topicId);
            } else {
                loadSheetContent(sheetId);
            }
            setActiveSublink(this);
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
            setActiveLink(this);
            closeMobileToc();
        });
    }

    function setActiveLink(element) {
        document.querySelectorAll('.toc-link').forEach(l => l.classList.remove('active'));
        document.querySelectorAll('.toc-sublink').forEach(l => l.classList.remove('active'));
        element.classList.add('active');
    }

    function setActiveSublink(element) {
        document.querySelectorAll('.toc-sublink').forEach(l => l.classList.remove('active'));
        document.querySelectorAll('.toc-link').forEach(l => l.classList.remove('active'));
        element.classList.add('active');
    }

    // ==================== CONTENT LOADING ====================

    function showLoading() {
        const contentArea = document.getElementById('dynamicContent');
        contentArea.innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2 text-muted">Loading...</p></div>';
        contentArea.style.display = 'block';
        document.getElementById('overviewSection').style.display = 'none';
    }

    function loadSheetContent(sheetId) {
        showLoading();
        const contentArea = document.getElementById('dynamicContent');

        fetch(`${baseUrl}/sheets/${sheetId}/content`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                contentArea.innerHTML = data.html;
            } else {
                contentArea.innerHTML = '<div class="alert alert-warning">Could not load content.</div>';
            }
        })
        .catch(() => {
            contentArea.innerHTML = '<div class="alert alert-danger">Failed to load content.</div>';
        });
    }

    function loadTopicContent(sheetId, topicId) {
        showLoading();
        const contentArea = document.getElementById('dynamicContent');

        fetch(`${baseUrl}/sheets/${sheetId}/topics/${topicId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                contentArea.innerHTML = data.html;
            } else {
                contentArea.innerHTML = '<div class="alert alert-warning">Could not load topic.</div>';
            }
        })
        .catch(() => {
            contentArea.innerHTML = '<div class="alert alert-danger">Failed to load topic.</div>';
        });
    }

    function loadAssessmentContent(sheetId, assessmentType) {
        showLoading();
        const contentArea = document.getElementById('dynamicContent');

        fetch(`${baseUrl}/sheets/${sheetId}/${assessmentType}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                contentArea.innerHTML = '<div class="assessment-inline-container">' + data.html + '</div>';
                bindAssessmentFormHandlers();
            } else {
                contentArea.innerHTML = data.html || '<div class="alert alert-info">No assessment available.</div>';
            }
        })
        .catch(() => {
            contentArea.innerHTML = '<div class="alert alert-danger">Failed to load assessment.</div>';
        });
    }

    // ==================== ASSESSMENT FORM HANDLING ====================

    function bindAssessmentFormHandlers() {
        const forms = document.querySelectorAll('#dynamicContent form[data-inline-submit]');
        forms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...';

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success !== undefined) {
                        const resultDiv = document.createElement('div');
                        resultDiv.className = 'assessment-result ' + (data.passed ? 'passed' : 'failed');
                        resultDiv.innerHTML = `
                            <i class="fas fa-${data.passed ? 'check-circle' : 'times-circle'} fa-3x mb-2"></i>
                            <div class="score-display">${data.score} / ${data.max_score}</div>
                            <p>${data.passed ? 'You passed!' : 'You did not pass. Try again.'}</p>
                        `;
                        this.replaceWith(resultDiv);
                    } else {
                        showNotification('Submission saved successfully!', 'success');
                        submitBtn.innerHTML = '<i class="fas fa-check me-1"></i> Submitted';
                    }
                })
                .catch(() => {
                    showNotification('Failed to submit. Please try again.', 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            });
        });
    }

    // ==================== MOBILE TOC ====================

    const tocToggle = document.getElementById('tocMobileToggle');
    const sidebar = document.querySelector('.sidebar-section');

    if (tocToggle && sidebar) {
        // Create overlay
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

    const moduleId = moduleData.dataset.moduleId;
    const courseId = moduleData.dataset.courseId;

    fetch(`/courses/${courseId}/module-${moduleId}/progress`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
    })
    .then(r => r.json())
    .then(data => {
        const progress = data.percentage || 0;
        const circle = document.getElementById('progressCircle');
        if (circle) {
            const circumference = 2 * Math.PI * 40;
            const offset = circumference - (progress / 100) * circumference;
            circle.style.strokeDasharray = circumference;
            circle.style.strokeDashoffset = offset;
        }
        const progressText = document.getElementById('progressText');
        if (progressText) progressText.textContent = Math.round(progress) + '%';

        const progressBadge = document.getElementById('progressBadge');
        if (progressBadge) progressBadge.textContent = Math.round(progress) + '%';

        const completedCount = document.getElementById('completedCount');
        if (completedCount && data.completed !== undefined) {
            completedCount.textContent = data.completed;
        }
    })
    .catch(() => {});

    // ==================== FOCUS MODE ====================

    const focusModeContainer = document.getElementById('focusModeContainer');
    const focusModeDataEl = document.getElementById('focusModeData');
    let focusModeData = [];
    let currentFocusIndex = 0;
    let currentImageIndex = 0;

    try {
        focusModeData = JSON.parse(focusModeDataEl.textContent);
    } catch (e) {}

    function enterFocusMode() {
        document.body.classList.add('focus-mode-active');
        focusModeContainer.classList.add('active');
        updateFocusContent();
        document.addEventListener('keydown', focusKeyHandler);
    }

    function exitFocusMode() {
        document.body.classList.remove('focus-mode-active');
        focusModeContainer.classList.remove('active');
        document.removeEventListener('keydown', focusKeyHandler);
    }

    function focusKeyHandler(e) {
        if (e.key === 'Escape') exitFocusMode();
        else if (e.key === 'ArrowRight' || e.key === ' ') { e.preventDefault(); nextFocusContent(); }
        else if (e.key === 'ArrowLeft') { e.preventDefault(); prevFocusContent(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); prevImage(); }
        else if (e.key === 'ArrowDown') { e.preventDefault(); nextImage(); }
    }

    function updateFocusContent() {
        const content = focusModeData[currentFocusIndex];
        if (!content) return;

        document.getElementById('focusModeTitle').textContent = content.title;
        document.getElementById('focusContentTitle').textContent = content.title;

        let bodyHtml = '';
        if (content.content) {
            bodyHtml += '<div class="mb-4">' + content.content.replace(/\n/g, '<br>') + '</div>';
        }
        if (content.parts && content.parts.length > 0) {
            content.parts.forEach(function (part, idx) {
                bodyHtml += '<div class="part-section mb-4 p-3 bg-light rounded">' +
                    '<h5><span class="badge bg-primary me-2">' + (idx + 1) + '</span>' + (part.title || '') + '</h5>' +
                    '<p>' + (part.explanation || '').replace(/\n/g, '<br>') + '</p>' +
                    '</div>';
            });
        }

        document.getElementById('focusContentBody').innerHTML = bodyHtml || '<p class="text-muted">No content available for this section.</p>';

        currentImageIndex = 0;
        updateFocusImage(content);

        document.getElementById('focusProgressBadge').textContent = (currentFocusIndex + 1) + ' / ' + focusModeData.length;
        document.getElementById('focusPrevBtn').disabled = currentFocusIndex === 0;
        document.getElementById('focusNextBtn').disabled = currentFocusIndex === focusModeData.length - 1;
    }

    function updateFocusImage(content) {
        const images = content.images || [];
        const noImage = document.getElementById('focusNoImage');
        const focusImage = document.getElementById('focusImage');
        const imageCaption = document.getElementById('focusImageCaption');
        const imageNav = document.getElementById('imageNav');
        const imageCounter = document.getElementById('imageCounter');

        if (images.length === 0) {
            noImage.style.display = 'block';
            focusImage.style.display = 'none';
            imageNav.style.display = 'none';
            imageCaption.textContent = '';
            imageCounter.textContent = '';
        } else {
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
        if (currentFocusIndex < focusModeData.length - 1) { currentFocusIndex++; updateFocusContent(); }
    }
    function prevFocusContent() {
        if (currentFocusIndex > 0) { currentFocusIndex--; updateFocusContent(); }
    }
    function nextImage() {
        const content = focusModeData[currentFocusIndex];
        if (currentImageIndex < (content.images || []).length - 1) { currentImageIndex++; updateFocusImage(content); }
    }
    function prevImage() {
        if (currentImageIndex > 0) { currentImageIndex--; updateFocusImage(focusModeData[currentFocusIndex]); }
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
    const saveOfflineText = document.getElementById('saveOfflineText');

    if ('caches' in window) {
        caches.has('module-' + moduleId).then(function (cached) {
            if (cached) {
                saveOfflineText.textContent = 'Saved';
                saveOfflineBtn.classList.remove('btn-outline-warning');
                saveOfflineBtn.classList.add('btn-warning');
            }
        });
    }

    saveOfflineBtn.addEventListener('click', async function () {
        if (!('serviceWorker' in navigator) || !navigator.serviceWorker.controller) {
            alert('Offline mode requires service worker support. Please refresh the page.');
            return;
        }

        saveOfflineBtn.disabled = true;
        saveOfflineBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

        try {
            const result = await window.cacheModuleForOffline(moduleId, window.location.href);
            if (result.success) {
                saveOfflineBtn.innerHTML = '<i class="fas fa-check me-1"></i> Saved';
                saveOfflineBtn.classList.remove('btn-outline-warning');
                saveOfflineBtn.classList.add('btn-success');
                showNotification('Module saved for offline viewing!', 'success');
            } else {
                throw new Error(result.error || 'Failed to save');
            }
        } catch (error) {
            saveOfflineBtn.innerHTML = '<i class="fas fa-cloud-download-alt me-1"></i> Save Offline';
            showNotification('Failed to save module for offline viewing.', 'error');
        } finally {
            saveOfflineBtn.disabled = false;
        }
    });

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
