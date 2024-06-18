
  document.addEventListener("DOMContentLoaded", function() {
    // Smooth scrolling for links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
          behavior: 'smooth'
        });
      });
    });

    // Fade-in effect for cards
    let cards = document.querySelectorAll('.card');
    cards.forEach(card => {
      card.style.opacity = 0;
      card.style.transform = 'translateY(20px)';
    });

    window.addEventListener('scroll', function() {
      cards.forEach(card => {
        if (card.getBoundingClientRect().top < window.innerHeight) {
          card.style.opacity = 1;
          card.style.transform = 'translateY(0)';
          card.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
        }
      });
    });

    // Modal auto show
    $('#announcementModal').modal('show');
  });

