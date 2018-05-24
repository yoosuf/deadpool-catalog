
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

function getGbpBuySellPrice(callback) {  
  var buy_result;
  var sell_result;

  client.getBuyPrice({'currencyPair': 'BTC-GBP'}, function(err, buy_price) {
    console.log(buy_price);
    buy_result = buy_price;
    client.getSellPrice({'currencyPair': 'BTC-GBP'}, function(err, sell_price) {
      console.log(sell_price);
      sell_result = sell_price;
      var buy_data = {
        "base": buy_result.data.base,
        "currency": buy_result.data.currency,
        "amount": buy_result.data.amount
        }
      var sell_data = {
        "base": sell_result.data.base,
        "currency": sell_result.data.currency,
        "amount": sell_result.data.amount
        }
      return callback({"data": {"buy" : buy_data, "sell":sell_data}});
    });
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

function getCadBuySellPrice(callback) {  
  var buy_result;
  var sell_result;

  client.getBuyPrice({'currencyPair': 'BTC-CAD'}, function(err, buy_price) {
    console.log(buy_price);
    buy_result = buy_price;
    client.getSellPrice({'currencyPair': 'BTC-CAD'}, function(err, sell_price) {
      console.log(sell_price);
      sell_result = sell_price;
      var buy_data = {
        "base": buy_result.data.base,
        "currency": buy_result.data.currency,
        "amount": buy_result.data.amount
        }
      var sell_data = {
        "base": sell_result.data.base,
        "currency": sell_result.data.currency,
        "amount": sell_result.data.amount
        }
      return callback({"data": {"buy" : buy_data, "sell":sell_data}});
    });
  });
}

module.exports.getGbpBuyPrice = getGbpBuyPrice;  
module.exports.getGbpSellPrice = getGbpSellPrice; 
module.exports.getGbpBuySellPrice = getGbpBuySellPrice; 
module.exports.getCadBuyPrice = getCadBuyPrice; 
module.exports.getCadSellPrice = getCadSellPrice; 
module.exports.getCadBuySellPrice = getCadBuySellPrice; 