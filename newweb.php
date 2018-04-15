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
            var address, wei, balance
            address = document.getElementById("address").value
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
        const main = async () => {
            // Who holds the token now?
            var myAddress = "0x8e0ccaD413555D3ABD2758fEeaCEb4B3479a760b";

            // Who are we trying to send this token to?
            var destAddress = "0x7Df73b0FbC274766451111408673C442E04C3211";

            // If your token is divisible to 8 decimal places, 42 = 0.00000042 of your token
            var transferAmount = 100000000;

            // Determine the nonce
            var count = await web3.eth.getTransactionCount(myAddress);
            console.log(`num transactions so far: ${count}`);

            // This file is just JSON stolen from the contract page on etherscan.io under "Contract ABI"
            var abiArray = JSON.parse(fs.readFileSync(path.resolve('http://localhost/test/mycoin.json'), 'utf-8'));

            // This is the address of the contract which created the ERC20 token
            var contractAddress = "0x7042dBa2b405E0D0d219337f5c027C1237448950";
            var contract = new web3.eth.Contract(abiArray, contractAddress, { from: myAddress });

            // How many tokens do I have before sending?
            var balance = await contract.methods.balanceOf(myAddress).call();
            console.log(`Balance before send: ${balance}`);

            // I chose gas price and gas limit based on what ethereum wallet was recommending for a similar transaction. You may need to change the gas price!
            var rawTransaction = {
                "from": myAddress,
                "nonce": "0x" + count.toString(16),
                "gasPrice": "0x003B9ACA00",
                "gasLimit": "0x250CA",
                "to": contractAddress,
                "value": "0x0",
                "data": contract.methods.transfer(destAddress, transferAmount).encodeABI(),
                "chainId": 0x01
            };

            var privKey = new Buffer('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', 'hex');
            var tx = new Tx(rawTransaction);
            tx.sign(privKey);
            var serializedTx = tx.serialize();

            // Comment out these three lines if you don't really want to send the TX right now
            console.log(`Attempting to send signed tx:  ${serializedTx.toString('hex')}`);
            var receipt = await web3.eth.sendSignedTransaction('0x' + serializedTx.toString('hex'));
            console.log(`Receipt info:  ${JSON.stringify(receipt, null, '\t')}`);

            // The balance may not be updated yet, but let's check
            balance = await contract.methods.balanceOf(myAddress).call();
            console.log(`Balance after send: ${balance}`);
        }
        function gettr2(){
            var count = web3.eth.getTransactionCount("0x8e0ccaD413555D3ABD2758fEeaCEb4B3479a760b");
            var abiArray = JSON.parse(fs.readFileSync('http://localhost/test/mycoin.json', 'utf-8'));
            var contractAddress = "0x7042dBa2b405E0D0d219337f5c027C1237448950";
            var contract = web3.eth.contract(abiArray).at(contractAddress);
            var rawTransaction = {
                "from": "0x8e0ccaD413555D3ABD2758fEeaCEb4B3479a760b",
                "nonce": web3.toHex(count),
                "gasPrice": "0x04e3b29200",
                "gasLimit": "0x7458",
                "to": "0x7042dBa2b405E0D0d219337f5c027C1237448950",
                "value": "0x0",
                "data": contract.transfer.getData("0x7Df73b0FbC274766451111408673C442E04C3211", 1000000000000000000, {from: "0x8e0ccaD413555D3ABD2758fEeaCEb4B3479a760b"}),
                "chainId": 0x03
            };

            var privKey = new Buffer('xxxxxxxxxxxxxxxxxxxxxxx', 'hex');
            var tx = new Tx(rawTransaction);

            tx.sign(privKey);
            var serializedTx = tx.serialize();

            web3.eth.sendRawTransaction('0x' + serializedTx.toString('hex'), function(err, hash) {
                if (!err)
                    console.log(hash);
                else
                    console.log(err);
            });
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
