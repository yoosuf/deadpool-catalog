
const key          = 'j6MZpI3Xi6qiJGf4IPX2Jlnv2zGin0LL/QbpuXIT6fmjuVlp4qk2QuuA'; // API Key
const secret       = 'lWMKf43m0Jo2YrxMgjUznODLYVEo9hejheKxvtIaYw2peDirJh7o2Fd6E8Pg98Otmz+PZSkJzildHSPzOv93dg=='; // API Private Key
const KrakenClient = require('kraken-api');
const kraken       = new KrakenClient(key, secret);

function getCadBuySellPrice(callback) {  

  (async () => {
    // Display user's balance
    // console.log(await kraken.api('Balance'));

    // Get Ticker Info
    await kraken.api('Ticker', { pair : 'XXBTZCAD' }, function(error, data) {
      console.log(data);

      var buy_data = {
        "base": "BTC",
        "currency": "CAD",
        "amount": data.result.XXBTZCAD.a[0]
        }
      var sell_data = {
        "base": "BTC",
        "currency": "CAD",
        "amount": data.result.XXBTZCAD.b[0]
        }

      return callback({"data": {"buy" : buy_data, "sell":sell_data}});
    })})();
}

module.exports.getCadBuySellPrice = getCadBuySellPrice; 