
var Client = require('coinbase').Client;
var client = new Client({'apiKey': '7ZwQTSfbA8MNHa9F',
                     'apiSecret': 'yswMi2RFI8dAFfPH563VafVDAXpu0ScS',
                    'version':'2018-05-22'});

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