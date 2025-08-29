document.addEventListener('DOMContentLoaded', () => {
    const starRatingContainer = document.querySelector('.star-rating');

    if (starRatingContainer) {
        const stars = starRatingContainer.querySelectorAll('label');
        const radios = starRatingContainer.querySelectorAll('input[type="radio"]');

        const resetStars = () => {
            let checkedIndex = -1;
            radios.forEach((radio, index) => {
                if (radio.checked) {
                    checkedIndex = index;
                }
            });

            stars.forEach((star, index) => {
                if (index <= checkedIndex) {
                    star.classList.add('selected');
                } else {
                    star.classList.remove('selected');
                }
            });
        };

        stars.forEach((star, index) => {
            star.addEventListener('mouseover', () => {
                stars.forEach((s, i) => {
                    if (i <= index) {
                        s.classList.add('hover');
                    } else {
                        s.classList.remove('hover');
                    }
                });
            });

            star.addEventListener('mouseout', () => {
                stars.forEach(s => s.classList.remove('hover'));
            });

            star.addEventListener('click', () => {
                radios[index].checked = true;
                resetStars();
            });
        });

        resetStars(); // Initialize on page load
    }
});