import express from "express";
import path from "path";
import logger from "morgan";
import bodyParser from "body-parser";
import cors from "cors";
import GraphHTTP from 'express-graphql';
import Schema from './graphql';

import index from "./routes/index";
import users from "./routes/users";

var app = express();

app.use(cors());
app.use(logger('dev'));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: false }));

app.use('/', index);
app.use('/users', users);


app.use('/graphql', GraphHTTP((request) => ({
  schema: Schema,
  context: { user: request.user },
  pretty: true,
  graphiql: true
})));


// catch 404 and forward to error handler
app.use(function(req, res, next) {
  var err = new Error('Not Found');
  err.status = 404;
  next(err);
});

// error handler
app.use(function(err, req, res, next) {
  // set locals, only providing error in development
  res.locals.message = err.message;
  res.locals.error = req.app.get('env') === 'development' ? err : {};

  // render the error page
  res.status(err.status || 500);
  res.json({
    message: err.message,
    error: err
  });
});

module.exports = app;
