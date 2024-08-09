import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["put"];

    connect() {
        //Nothing to do
    }

    /**
     *
     * @param event {Event}
     */
    put(event) {
        event.preventDefault();
        const url = this.element.dataset.url;

        fetch(url, {
            method: 'PUT',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
        })
            .then(response => {
                if (response.ok) {
                    response.text()
                        .then(message => {
                            alert(message);
                            window.location.reload();
                        });
                } else {
                    response.text()
                        .then(message => {
                            console.error(message);
                        });
                    throw new Error('Failed to update');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating.');
            });
    }
}
