const {merge} = require('webpack-merge');
const common = require('./webpack.config.js');
const TerserPlugin = require("terser-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = merge(common, {
    mode: 'production',
    devtool: false,
    watch: false,
    optimization: {
        minimize: true,
        minimizer: [new TerserPlugin({extractComments: true})],
    },
    output: {
        filename: 'Resources/Public/JavaScript/dist/[name].min.js',
    },
    plugins: [new MiniCssExtractPlugin({
        filename: 'Resources/Public/Css/dist/[name].min.css',
    })],
});
