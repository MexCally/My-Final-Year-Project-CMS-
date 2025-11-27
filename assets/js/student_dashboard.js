 // Navigation functionality
 document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.nav-link[data-section]');
    const contentSections = document.querySelectorAll('.content-section');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all nav links
            navLinks.forEach(nl => nl.classList.remove('active'));
            
            // Add active class to clicked link
            this.classList.add('active');
            
            // Hide all content sections
            contentSections.forEach(section => {
                section.style.display = 'none';
            });
            
            // Show selected section
            const targetSection = this.getAttribute('data-section');
            document.getElementById(targetSection).style.display = 'block';
        });
    });

    // Course selection functionality
    const courseCheckboxes = document.querySelectorAll('input[type="checkbox"]');
    courseCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Add logic here for dynamic course selection updates
            console.log('Course selection changed:', this.id, this.checked);
        });
    });

    // Smooth animations for cards
    const cards = document.querySelectorAll('.dashboard-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});