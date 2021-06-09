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
        document.getElementById('bidd-price-btn');

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

            document.getElementById('bidders').appendChild(biddersList);
        })
}

function biddPrice() {
    fetch('http://127.0.0.1:8000/users')
        .then(response => response.json())
        .then(user => {
            fetch('http://127.0.0.1:8000/auctions/' + auctionId)
                .then(response => response.json())
                .then(auction => {
                    let price = document.getElementById('price');
                    let button = document.getElementById('bidd-offer');
                    let message = new Object();
                    message.id = auctionId
                    message.biddOffer = document.getElementById('bidd-price-btn').value;
                    message.username = user['username'];

                    if (message.biddOffer <= auction['price']) {
                        throw 'Nie możesz zaproponować takiej samej lub niższej ceny.';
                    }
                    if (auction['0']['lastBidd']['username'] === user['username']) {
                        throw 'Nie możesz przebić swojej własnej oferty.';
                    }

                    let lastBidd = document.getElementById('last-bidd');
                    lastBidd.innerText = 'Ostatnia licytacja ' + document.getElementById('bidd-price-btn').value
                        + ' zł: ' + user['username'];
                    price.innerText = 'Cena: ' + message.biddOffer + 'zł';
                    button.disable = true;
                    websocket.send(JSON.stringify(message));
                }).catch(error => {
                    alert(error)
                });
        })
        .catch(error => {
            console.log(error);
        });
}