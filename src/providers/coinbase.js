const converter = require('google-currency');

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
  var prof;

  client.getBuyPrice({'currencyPair': 'BTC-GBP'}, function(err, buy_price) {

    buy_result = buy_price;
    client.getSellPrice({'currencyPair': 'BTC-GBP'}, function(err, sell_price) {

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

function calculate (buy_value, sell_value, buy_currency, sell_currency, amount, callback) {

  var result = [];
  console.log('b val '+buy_value);


  if (buy_value === undefined || sell_value === undefined || amount === undefined) {
      return json({error: false, message: 'NOT COMPLETE: Request data not complete'});
  }

  const val = (parseFloat(amount) / parseFloat(buy_value)) * parseFloat(sell_value);

  const options = {
      from: sell_currency,
      to: buy_currency,
      amount: val
  };


  console.log(`Calculated Val ${val}`)


  converter(options).then(value => {

      console.log(`google Val ${value.converted}`)

      console.log(`SRC Val ${amount}`)

      let calculatedVal =  value.converted - amount;

      //return ({"data": {"value": calculatedVal}});
      console.log(`calculatedVal ${calculatedVal}`)
      return callback(calculatedVal);  

  }).catch(e => {
    //return callback(1000);  
    return callback({"Error": {"error": e}})

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