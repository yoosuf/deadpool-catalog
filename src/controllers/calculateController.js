// var Provider = require('../models/provider');
var coinbase = require('../providers/coinbase');
var kraken = require('../providers/kraken');

// Display list of all Providers.
exports.calculate = function(req, res) {
    // console.log(req);

    if(req.query.buy_value == undefined || req.query.sell_value == undefined || req.query.amount == undefined)
        res.send('NOT COMPLETE: Request data not complete');
    
    var val =  (parseFloat(req.query.amount) / parseFloat(req.query.buy_value)) * parseFloat(req.query.sell_value)
    console.log(val);

    res.send({"data": {"value" : val}});
};
