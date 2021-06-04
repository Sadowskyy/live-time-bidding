let auctionId = window.location.href.replace('http://127.0.0.1:8000/aukcja/', '')
const websocket = new WebSocket("ws://localhost:3200")

websocket.onopen = function (evt) {

}
websocket.onmessage = function (evt) {
    console.log(evt.data);
    console.log(evt);
    // id = JSON.parse(evt.data['id'])
    //
    // console.log(id)
    // if (id === auctionId) location.reload()
};
websocket.onclose = function (evt) {

};

websocket.onerror = function (evt) {
    alert("ERROR")
}


window.onload = function () {
    let body = document.body;
    let image = document.getElementById('image');
    let name = document.getElementById('name');
    let price = document.getElementById('price');
    let bidders = document.getElementById('bidd-list');
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
                lastBidder.innerText = 'Ostatnia licytacja ' + json['price'] + ' zł: ' + json['0']['lastBidd']['username'];
                biddersList.appendChild(lastBidder);
            }

            name.innerText = json['name'];
            price.innerHTML = 'Cena: ' + json['price'] + 'zł';
            image.src = json['image'];

            document.getElementById('bidders').appendChild(biddersList);
            // json['bidders'].forEach(function(name){
            //     var li = document.createElement('li');
            //     biddersList.appendChild(li);
            //     li.innerHTML += name;
            // });

            // const ul = document.createElement('ul');
            // document.getElementById('bidders').appendChild(ul);
            // var li = document.createElement('li');
            // ul.appendChild(li);
            // li.innerText = 'xddd';
            // biddersList.appendChild(lastBidder);
        })
}

function biddPrice() {
    fetch('http://127.0.0.1:8000/users')
        .then(response => response.json())
        .then(json => {
            message.id = auctionId
            message.biddOffer = document.getElementById('bidd-price-btn').value;
            message.username = json['username'];

            websocket.send(JSON.stringify(message));
        })
        .catch(error => {
            console.error(error);
        });
    let message = new Object();

}