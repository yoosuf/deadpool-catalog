var express = require('express');
var router = express.Router();

/* GET home page. */
router.get('/', function(req, res, next) {
  res.json({
    message: "hell world sdsdsds",
    error: 200
  });
});

module.exports = router;
