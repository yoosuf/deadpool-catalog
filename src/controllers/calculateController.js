// var Provider = require('../models/provider');
const coinbase = require('../providers/coinbase');
const kraken = require('../providers/kraken');
const converter = require('google-currency')


// Display list of all Providers.
exports.calculate = function (req, res) {
    if (req.query.buy_value === undefined || req.query.sell_value === undefined || req.query.amount === undefined) {
        res.json({error: true, message: 'NOT COMPLETE: Request data not complete'}, 400);
    }
    const val = (parseFloat(req.query.amount) / parseFloat(req.query.buy_value)) * parseFloat(req.query.sell_value);

    const options = {
        from: req.query.sell_currency,
        to: req.query.buy_currency,
        amount: val
    };
    console.log(`Calculated Val ${val}`)
    converter(options).then(value => {
        console.log(`google Val ${value.converted}`)
        console.log(`SRC Val ${req.query.amount}`)
        let calculatedVal =  value.converted - req.query.amount;
        console.log(`results Val ${calculatedVal}`)
        res.json({ error: false, "data": {"value": calculatedVal}}, 200);
    }).catch(e => {
        res.json({error: true, message: e});
    });


};



