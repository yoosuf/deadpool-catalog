var Provider = require('../models/provider');
var coinbase = require('../providers/coinbase');
var kraken = require('../providers/kraken');
// import coinbase from "../providers/coinbase";

// Display list of all Providers.

//Coinbase
exports.coinbase_buy_gbp = function(req, res) {
    coinbase.getGbpBuyPrice(function(response){
        // Here you have access to your variable
        console.log(response);
        res.send(response);
    })                 
};

exports.coinbase_sell_gbp = function(req, res) {
    coinbase.getGbpSellPrice(function(response){
        // Here you have access to your variable
        console.log(response);
        res.send(response);
    })                 
};

exports.coinbase_buy_sell_gbp = function(req, res) {
    coinbase.getGbpBuySellPrice(function(response){
        // Here you have access to your variable
        console.log(response);
        res.send(response);
    })                 
};

exports.coinbase_buy_cad = function(req, res) {
    coinbase.getCadBuyPrice(function(response){
        // Here you have access to your variable
        console.log(response);
        res.send(response);
    })                 
};

exports.coinbase_sell_cad = function(req, res) {
    coinbase.getCadSellPrice(function(response){
        // Here you have access to your variable
        console.log(response);
        res.send(response);
    })                 
};

exports.coinbase_buy_sell_cad = function(req, res) {
    coinbase.getCadBuySellPrice(function(response){
        // Here you have access to your variable
        console.log(response);
        res.send(response);
    })                 
};

//Kraken
exports.kraken_buy_sell_cad = function(req, res) {
    kraken.getCadBuySellPrice(function(response){
        // Here you have access to your variable
        console.log(response);
        res.send(response);
    })                 
};

// Display list of all Providers.
exports.provider_list = function(req, res) {
    res.send('NOT IMPLEMENTED: Provider list');
};

// Display detail page for a specific Provider.
exports.provider_detail = function(req, res) {
    res.send('NOT IMPLEMENTED: Provider detail: ' + req.params.id);
};

// Display Provider create form on GET.
exports.provider_create_get = function(req, res) {
    res.send('NOT IMPLEMENTED: Provider create GET');
};

// Handle Provider create on POST.
exports.provider_create_post = function(req, res) {
    res.send('NOT IMPLEMENTED: Provider create POST');
};

// Display Provider delete form on GET.
exports.provider_delete_get = function(req, res) {
    res.send('NOT IMPLEMENTED: Provider delete GET');
};

// Handle Provider delete on POST.
exports.provider_delete_post = function(req, res) {
    res.send('NOT IMPLEMENTED: Provider delete POST');
};

// Display Provider update form on GET.
exports.provider_update_get = function(req, res) {
    res.send('NOT IMPLEMENTED: Provider update GET');
};

// Handle Provider update on POST.
exports.provider_update_post = function(req, res) {
    res.send('NOT IMPLEMENTED: Provider update POST');
};