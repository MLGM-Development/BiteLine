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
            if (file.type.match('image.*')) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    uploadPlaceholder.style.display = 'none';
                    imagePreview.style.display = 'flex';
                }

                reader.readAsDataURL(file);
            }
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
    });
});