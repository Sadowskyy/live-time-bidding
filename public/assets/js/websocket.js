let auctionId = window.location.href.replace('http://127.0.0.1:8000/aukcja/', '')
const websocket = new WebSocket("ws://localhost:3200")

websocket.onopen = function (evt) {

}
websocket.onmessage = function (evt) {
    var data = JSON.parse(evt.data);
    console.log(data['id'] === parseInt(auctionId));
    console.log(data['id']);
    console.log(parseInt(auctionId))
    if (data['id'] === parseInt(auctionId)) {
        let lastBidd = document.getElementById('last-bidd');
        let price = document.getElementById('price');

        price.innerText = 'Cena: ' + data['price'] + 'zł';
        lastBidd.innerText = 'Ostatnia licytacja ' + data['price'] + ' zł: ' + data['0']['lastBidd']['username'];
    }
};

websocket.onclose = function (evt) {

};

websocket.onerror = function (evt) {
    alert("ERROR")
}


window.onload = function () {
    let image = document.getElementById('image');
    let name = document.getElementById('name');
    let price = document.getElementById('price');
    let biddButton = document.getElementById('bidd-price-btn');
    fetch('http://127.0.0.1:8000/auctions/' + auctionId)
        .then(response => response.json())
        .then(json => {
            console.log(json)
            const biddersList = document.createElement('ul');

            if (biddButton !== null) {
                biddButton.min = json['price'] + 1;
            }
            biddersList.className = 'list-group';
            if (json['0']['lastBidd']['username'] !== null) {
                const lastBidder = document.createElement('li')
                lastBidder.className = 'list-group-item active';
                lastBidder.id = 'last-bidd'
                lastBidder.innerText = 'Ostatnia licytacja ' + json['price'] + ' zł: ' + json['0']['lastBidd']['username'];
                biddersList.appendChild(lastBidder);
            }

            name.innerText = json['name'];
            price.innerHTML = 'Cena: ' + json['price'] + 'zł';
            image.src = json['image'];

            document.getElementById('bidders').appendChild(biddersList);
        })
}

function biddPrice() {
    fetch('http://127.0.0.1:8000/users')
        .then(response => response.json())
        .then(json => {
            let message = new Object();
            let lastBidd = document.getElementById('last-bidd');
            lastBidd.innerText = 'Ostatnia licytacja ' + document.getElementById('bidd-price-btn').value
                + ' zł: ' + json['username'];

            message.id = auctionId
            message.biddOffer = document.getElementById('bidd-price-btn').value;
            message.username = json['username'];

            websocket.send(JSON.stringify(message));
        })
        .catch(error => {
            console.error(error);
        });
}