// import { saveDestination } from './utils/storage.js';

document.addEventListener('DOMContentLoaded', () => {
    const pinButtons = document.querySelectorAll('.pin-destination');
    const setDestinationBtns = document.querySelectorAll('.set-destination-btn');
    const notification = document.getElementById('notification');
    const notificationMessage = document.getElementById('notification-message');
  
    pinButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log("Something");
            const campus = this.getAttribute('data-campus');
            setDefaultCampus(campus);
        });
    });


    setDestinationBtns.forEach(button => {
        button.addEventListener('click', function() {
            const campus = this.getAttribute('data-campus');
            setSessionCampus(campus);
        });
    });

    function setSessionCampus(campus) {
        fetch('choose_campus.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `campus=${campus}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '../splash/splash.html';
            } else {
                showNotification('Failed to set destination. Please try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');
        });
    }

    function setDefaultCampus(campus) {
        fetch('set_default_campus.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `set_default_campus=true&campus=${campus}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
         
                pinButtons.forEach(btn => {
                    if (btn.getAttribute('data-campus') === campus) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });
                showNotification('Default campus set successfully!');
             
                setTimeout(() => {
                    window.location.href = '../splash/splash.html';
                }, 2000);
            } else {
                showNotification('Failed to set default campus. Please try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');
        });
    }

    function showNotification(message, type = 'success') {
        notificationMessage.textContent = message;
        notification.className = 'notification show';
        notification.style.backgroundColor = type === 'error' ? '#EF5350' : '#4CAF50';
        setTimeout(() => {
            notification.className = 'notification';
        }, 3000);
    }
});

