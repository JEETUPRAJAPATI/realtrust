import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Pusher Configuration
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});

let audioInteractionDone = false;
window.Echo.channel('property-added')
    .listen('.message.sent', (e) => {
        console.log('Received notification:', e);
        const notificationUrl = `/staff/properties/view/${e.property_id}`;

        // Add notification to the UI with improved styling and transitions
        $('#notification-list').prepend(`
            <li class="notification-item unread" id="notification-${e.id}">
                <a href="${notificationUrl}" class="notification-link d-flex align-items-center" data-id="${e.id}">
                    <div class="icon-circle bg-success text-white">
                        <i class="material-icons">person_add</i>
                    </div>
                    <div class="menu-info ms-3">
                        <h6 class="mb-1">${e.message}</h6>
                        <p class="text-muted small"><i class="material-icons">access_time</i> just now</p>
                    </div>
                </a>
            </li>
        `);

        // Play the notification sound
        playNotificationSound();
        fetchUnreadCount();
    });

// Listen for the 'property-visit-scheduled' event
window.Echo.channel('schedule_visit')
    .listen('.property.visit.scheduled', (e) => {
        console.log('Received notification:', e);
        const notificationUrl = `/staff/schedule_properties/visit/${e.property_id}`;

        // Add notification to the UI with improved styling and transitions
        $('#notification-list').prepend(`
            <li class="notification-item unread" id="notification-${e.id}">
                <a href="${notificationUrl}" class="notification-link d-flex align-items-center" data-id="${e.id}">
                    <div class="icon-circle bg-warning text-white">
                        <i class="material-icons">schedule</i>
                    </div>
                    <div class="menu-info ms-3">
                        <h6 class="mb-1">${e.message}</h6>
                        <p class="text-muted small"><i class="material-icons">access_time</i> just now</p>
                    </div>
                </a>
            </li>
        `);

        // Play the notification sound
        playNotificationSound();
        fetchUnreadCount();
    });

// Track user interaction to allow audio playback
document.addEventListener('click', function () {
    audioInteractionDone = true;
});

// Play the notification sound only after user interaction
function playNotificationSound() {
    if (audioInteractionDone) {
        let audio = new Audio('/sounds/notification.mp3');
        audio.play().catch(function (error) {
            console.log("Audio play failed: ", error);
        });
    } else {
        console.log("User has not interacted with the page yet. Audio blocked.");
    }
}
