<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <script type="text/javascript" src="web3.min.js"></script>
        <script type="text/javascript">
            window.addEventListener('load', function () {
                if (typeof web3 !== 'undefined') {
                    console.log('Web3 Detected! ' + web3.currentProvider.constructor.name)
                    window.web3 = new Web3(web3.currentProvider);
                } else {
                    console.log('No Web3 Detected... using HTTP Provider')
                    window.web3 = new Web3(new Web3.providers.HttpProvider("https://ropsten.infura.io/FW0Ha0VrUL6dtmWAkyRU"));
                }
            })
            function getBalance() {
                var address, wei, balance;
                address = document.getElementById("address").value;
                try {
                    web3.eth.getBalance(address, function (error, wei) {
                        if (!error) {
                            var balance = web3.fromWei(wei, 'ether');
                            document.getElementById("output").innerHTML = balance + " ETH";
                        }
                    });
                } catch (err) {
                    document.getElementById("output").innerHTML = err;
                }
            }
        </script>
    </head>
    <body>
        <p>Enter your Ethereum Address:</p>
        <input type="text" size="50" id="address" />
        <button type="button" onClick="getBalance();">Get Balance</button>
        <br />
        <br />
        <div id="output"></div>
    </body>
</html>