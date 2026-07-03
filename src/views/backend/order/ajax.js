/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

document.addEventListener("DOMContentLoaded", function () {

    const csrf_token = document.head.querySelector('[name="csrf-token"]').getAttribute("content");
    const payment_block = document.getElementById('data-from-payment-service');

    paymentDataLoad().catch(r => (console.log(r)));

    async function paymentDataLoad(event, target) {

        const formData = new FormData();
        formData.append('id', payment_block.dataset.id);

        const responsePromise = await fetch(payment_block.dataset.url, {
            method: 'POST',
            headers: getHeaders(),
            body: formData
        });
        processDataToCard(responsePromise).catch(r => (console.log(r)));
    }

    async function processDataToCard(responsePromise) {
        if (responsePromise.ok) {
            const e = await responsePromise.json();
            if (e.status === 'success') {
                payment_block.innerHTML = e.data;
            }
            if (e.status === 'error') {
                console.log(e.message);
                iziToast.error({
                    position: 'topCenter',
                    message: e.message + ': ' + e.data,
                    title: e.status,
                });
            }
        } else {
            const error = "HTTP error: " + responsePromise.status;
            console.log(error);
            iziToast.error({
                position: 'topCenter',
                message: error,
                title: responsePromise.status,
            });
        }
    }

    function getHeaders() {
        const headers = {};
        headers['x-csrf-token'] = csrf_token;
        headers['X-Requested-With'] = 'XMLHttpRequest';
        headers['X-Requested-With-Fetch'] = true;
        return headers;
    }

});
