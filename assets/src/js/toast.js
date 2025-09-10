import $ from 'jquery';
import * as ToastModule from 'bootstrap/js/src/toast';

$(document).ready(function () {
    if (typeof localize !== 'undefined') {
        let startTime = new Date(localize.start_time).toISOString();

        async function checkForNewPost() {
            try {
                let url = `${localize.resturl}?after=${startTime}&per_page=1&orderby=date&order=desc`;
                let response = await fetch(url);
                let posts = await response.json();

                if (posts.length > 0) {
                    let latest = posts[0];

                    $('#newPostLink')
                        .attr('href', latest.link)
                        .text(latest.title.rendered);

                    let toastEl = document.getElementById('newPostToast');
                    let toast = new ToastModule.default(toastEl, {
                        autohide: true,
                        delay: 30000
                    });
                    toast.show();

                    startTime = new Date(latest.date).toISOString();
                }
            } catch (err) {
                console.error("Error checking for new post:", err);
            }
        }

        setInterval(checkForNewPost, 60000);
    }
});
