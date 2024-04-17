document.addEventListener("DOMContentLoaded", function() {
    const zoomIcons = document.querySelectorAll('.zoom-icon');
    const modal = document.getElementById('myModal');
    const modalImg = document.getElementById("modal-img");
    const closeModal = document.getElementsByClassName("close")[0];
    const heartIcons = document.querySelectorAll('.heart-icon'); // Select heart icons correctly

    zoomIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const publicationItem = this.closest('.publication-item');
            const publicationImage = publicationItem.querySelector('.publication-image').src;

            modal.style.display = "block";
            modalImg.src = publicationImage;
        });
    });

    heartIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const currentSrc = this.src;
            if (currentSrc.includes('emptyHeart.png')) {
                this.src = "{{ asset('images/fullheart.png') }}";
            } else {
                this.src = "{{ asset('images/emptyHeart.png') }}";
            }
        });
    });

    // Close the modal when clicked outside the image or on the close button
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }

    closeModal.onclick = function() {
        modal.style.display = "none";
    }
});

 document.addEventListener("DOMContentLoaded", function() {
            const commentIcon = document.getElementById('comment-icon');
            if (commentIcon) {
                commentIcon.addEventListener('click', function() {
                    window.location.href = "{{ path('app_comments') }}"; // Use the route name defined in your Symfony routes
                });
            }
        });
