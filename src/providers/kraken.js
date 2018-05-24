
const key          = '...'; // API Key
const secret       = '...'; // API Private Key
const KrakenClient = require('kraken-api');
const kraken       = new KrakenClient(key, secret);
 
(async () => {
    // Display user's balance
    console.log(await kraken.api('Balance'));
 
    // Get Ticker Info
    console.log(await kraken.api('Ticker', { pair : 'XXBTZUSD' }));
})();

function getGbpBuyPrice(callback) {  
    client.getBuyPrice({'currencyPair': 'BTC-GBP'}, function(err, price) {
            console.log(price);
            return callback(price);
          });
}

function getGbpSellPrice(callback) {  
  client.getSellPrice({'currencyPair': 'BTC-GBP'}, function(err, price) {
          console.log(price);
          return callback(price);
        });
}

function getCadBuyPrice(callback) {  
  client.getBuyPrice({'currencyPair': 'BTC-CAD'}, function(err, price) {
          console.log(price);
          return callback(price);
        });
}

function getCadSellPrice(callback) {  
  client.getSellPrice({'currencyPair': 'BTC-CAD'}, function(err, price) {
          console.log(price);
          return callback(price);
        });
}

module.exports.getGbpBuyPrice = getGbpBuyPrice;  
module.exports.getGbpSellPrice = getGbpSellPrice; 
module.exports.getCadBuyPrice = getCadBuyPrice; 
module.exports.getCadSellPrice = getCadSellPrice; 