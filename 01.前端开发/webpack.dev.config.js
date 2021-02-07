/*
 * @Author: your name
 * @Date: 2019-12-31 11:07:14
 * @LastEditTime : 2020-01-17 09:32:03
 * @LastEditors  : Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: \02.manage\webpack.dev.config.js
 */
const webpack = require('webpack');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const merge = require('webpack-merge');
const webpackBaseConfig = require('./webpack.base.config.js');
const fs = require('fs');
const path = require('path');
fs.open('./src/config/env.js', 'w', function (err, fd) {
    const buf = 'export default "development";';
    //fs.write(fd, buf, 0, buf.length, 0, function (err, written, buffer){});
    fs.write(fd, buf, 0, 'utf-8', function (err, written, buffer) { });
});

module.exports = merge(webpackBaseConfig, {
    resolve: {
        alias: {
            lib: path.resolve(__dirname, 'src/libs/'),
        }
    },
    devtool: '#source-map',
    output: {
        publicPath: '/dist/',
        filename: '[name].js',
        chunkFilename: '[name].chunk.js'
    },
    plugins: [
        new ExtractTextPlugin({
            filename: '[name].css',
            allChunks: true
        }),
        new webpack.optimize.CommonsChunkPlugin({
            name: 'vendors',
            filename: 'vendors.js'
        }),
        new HtmlWebpackPlugin({
            filename: '../index.html',
            template: './src/template/index.ejs',
            inject: false
        })
    ],
    devServer: {
        host: '127.0.0.1',
        port: 8080,
        historyApiFallback: false,
        noInfo: true,
        headers: { 'X-Content-Type': 'text/html; charset=UTF-8' },
        proxy: {
            '/index.php': {
                target: 'http://question.local.com/index.php',
                changeOrigin: true,
                // pathRewrite: { '^/api': '' }
            }
        }
    }

});