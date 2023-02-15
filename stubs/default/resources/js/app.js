import './bootstrap';

document.addEventListener('click', function (event) {
    if (event.target.matches('[data-modal="composeModal"]') || event.target.matches('[data-target="#composeModal"]')) {
        var modal = document.querySelector('#composeModal');
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            modal.classList.add('sm:flex');
            modal.setAttribute('aria-hidden', 'false');
            modal.setAttribute('style', 'display: flex!important');
        } else {
            modal.classList.remove('sm:flex');
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            modal.setAttribute('style', 'display: none!important');
        }
    }
});
document.addEventListener('click', function (event) {
    if (event.target.matches('[data-modal="viewModal"]') || event.target.matches('[data-target="#viewModal"]')) {
        var modal = document.querySelector('#viewModal');
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            modal.classList.add('sm:flex');
            modal.setAttribute('aria-hidden', 'false');
            modal.setAttribute('style', 'display: flex!important');
        } else {
            modal.classList.remove('sm:flex');
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            modal.setAttribute('style', 'display: none!important');
        }
    }
});

$(document).ready(function () {
    // // When the view button is clicked
    // $('.view-button').click(function() {
    //     // Get the message ID from the button's data-message-id attribute
    //     var id = $(this).data('message-id');
    //
    //     // Set the message ID in the modal
    //     $('#viewModal').data('message-id', id);
    // });
    document.querySelectorAll('.view-button').forEach(function (button) {
        button.addEventListener('click', function (event) {
            var messageId = this.getAttribute('data-message-id');
            document.getElementById('messageId').value = messageId;
            document.getElementById('decryptForm').action = '/' + PGP_PREFIX + '/messages/' + messageId + '/thread';
        });
    });

    document.querySelectorAll('.open-view-modal').forEach(function (element) {
        element.addEventListener('click', function (event) {
            event.preventDefault(); // prevent the link from navigating to a different page
            var messageId = this.getAttribute('data-message-id');
            document.getElementById('messageId').value = messageId; // set the value of the hidden input
            document.getElementById('decryptForm').action = '/' + PGP_PREFIX + '/messages/' + messageId + '/thread';
        });
    });


    document.getElementById('decryptButton').addEventListener('click', function (event) {
        event.preventDefault(); // prevent the button from submitting the form
        var spinner = '<i class="fas fa-spinner fa-spin"></i> Decrypting...';
        this.innerHTML = spinner; //show spinner
        document.getElementById('decryptForm').submit(); // submit the form
    });

    //Replies

    // Get the form element
    const form = document.querySelector("form");

// Add a submit event listener to the form
    form.addEventListener("submit", event => {
        event.preventDefault();

        // Create a FormData object to store the form data
        const formData = new FormData(form);

        // Use the Fetch API to send a POST request to the server
        fetch(form.action, {
            method: "POST",
            body: formData
        })
            .then(response => response.text())
            .then(responseText => {
                const container = document.querySelector(".fixed.bottom-0.right-0");
                container.innerHTML = responseText;
                container.style.display = "block";
            })
            .catch(error => console.error(error));
    });
});
