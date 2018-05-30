var https = require('https');


export function superScript(string) {
    return string;
}


//http://data.fixer.io/api/convert
//     ? access_key = API_KEY
//     & from = GBP
//     & to = JPY
//     & amount = 25

export function convertCurrency(fromCurrency, toCurrency, amount) {

    var apiKey = 'access_key=2a052e5ae8296c3dc37062b4a2c39be8';
    fromCurrency = encodeURIComponent(fromCurrency);
    toCurrency = encodeURIComponent(toCurrency);
    var query = `&from=${fromCurrency}&to=${toCurrency}&amount=${amount}`;
    var url = 'http://data.fixer.io/api/convert?' + apiKey + query;

    console.log(url);

    https.get(url, function (res) {
        var body = '';

        res.on('data', function (chunk) {
            body += chunk;
        });

        res.on('end', function () {
            try {
                var jsonObj = JSON.parse(body);

                var val = jsonObj[query];
                if (val) {
                    var total = val * amount;
                    cb(null, Math.round(total * 100) / 100);
                } else {
                    var err = new Error("Value not found for " + query);
                    console.log(err);
                    cb(err);
                }
            } catch (e) {
                console.log("Parse error: ", e);
                cb(e);
            }
        });
    }).on('error', function (e) {
        console.log("Got an error: ", e);
        cb(e);
    });
}