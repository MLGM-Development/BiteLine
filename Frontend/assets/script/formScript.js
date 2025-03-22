document.addEventListener('DOMContentLoaded', () => {
    const fadeElements = document.querySelectorAll('.fade-in-up');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, { threshold: 0.1 });

    fadeElements.forEach(element => {
        observer.observe(element);
    });

});

document.addEventListener('DOMContentLoaded', () => {
    const imageUploadArea = document.getElementById('imageUploadArea');
    const imageUpload = document.getElementById('imageUpload');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const removeImage = document.getElementById('removeImage');

    // Open file dialog when clicking on the upload area
    imageUploadArea.addEventListener('click', () => {
        imageUpload.click();
    });

    // Handle keyboard accessibility
    imageUploadArea.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            imageUpload.click();
        }
    });

    // Handle file selection
    imageUpload.addEventListener('change', handleFileSelect);

    // Handle drag and drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        imageUploadArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        imageUploadArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        imageUploadArea.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        imageUploadArea.classList.add('drag-over');
    }

    function unhighlight() {
        imageUploadArea.classList.remove('drag-over');
    }

    imageUploadArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length) {
            handleFiles(files);
        }
    }

    function handleFiles(files) {
        if (files[0]) {
            const file = files[0];
            const fileType = file.type;

            // Show preview based on file type
            uploadPlaceholder.style.display = 'none';
            imagePreview.style.display = 'flex';

            if (fileType.match('image.*')) {
                // Handle image files
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
                    // Remove any previously added document icon
                    const existingIcon = imagePreview.querySelector('.document-icon');
                    if (existingIcon) {
                        existingIcon.remove();
                    }
                }
                reader.readAsDataURL(file);
            } else {
                // Handle document files (PDF, DOC, DOCX)
                previewImg.style.display = 'none';

                // Remove any previously added document icon
                const existingIcon = imagePreview.querySelector('.document-icon');
                if (existingIcon) {
                    existingIcon.remove();
                }

                // Create document icon with file name
                const docIcon = document.createElement('div');
                docIcon.className = 'document-icon';

                let iconSvg = '';
                if (fileType === 'application/pdf') {
                    // PDF icon
                    iconSvg = `<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <path d="M14 2v6h6"></path>
                        <path d="M16 13H8"></path>
                        <path d="M16 17H8"></path>
                        <path d="M10 9H8"></path>
                    </svg>`;
                } else if (fileType === 'application/msword' || fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                    // DOC/DOCX icon
                    iconSvg = `<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <path d="M14 2v6h6"></path>
                        <path d="M16 13H8"></path>
                        <path d="M16 17H8"></path>
                        <path d="M10 9H8"></path>
                    </svg>`;
                } else {
                    // Generic document icon
                    iconSvg = `<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <path d="M14 2v6h6"></path>
                        <path d="M16 13H8"></path>
                        <path d="M16 17H8"></path>
                        <path d="M10 9H8"></path>
                    </svg>`;
                }

                docIcon.innerHTML = `
                    ${iconSvg}
                    <p class="file-name">${file.name}</p>
                    <p class="file-size">${formatFileSize(file.size)}</p>
                `;

                imagePreview.appendChild(docIcon);
            }
        }
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) {
            return bytes + ' bytes';
        } else if (bytes < 1048576) {
            return (bytes / 1024).toFixed(1) + ' KB';
        } else {
            return (bytes / 1048576).toFixed(1) + ' MB';
        }
    }

    function handleFileSelect(e) {
        const files = e.target.files;
        handleFiles(files);
    }

    // Remove image preview
    removeImage.addEventListener('click', (e) => {
        e.stopPropagation(); // Prevent triggering the upload area click
        imageUpload.value = '';
        uploadPlaceholder.style.display = 'flex';
        imagePreview.style.display = 'none';
        previewImg.src = '';

        // Remove any document icon if present
        const existingIcon = imagePreview.querySelector('.document-icon');
        if (existingIcon) {
            existingIcon.remove();
        }
    });
});