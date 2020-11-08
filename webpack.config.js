const HtmlWebPackPlugin = require('html-webpack-plugin');
const htmlPlugin = new HtmlWebPackPlugin({
    template: './app/html/chihuahua/index.html',
    filename: 'index.html'
});
const path = require('path');

module.exports = {
    entry: './app/src/js/app.js',
    output: {
        path: path.resolve(__dirname, 'app/html/chihuahua/js'),
        filename: 'app.js'
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_module/,
                use: {
                    loader: "babel-loader",
                },
            },
        ],
    },
    plugins: [htmlPlugin],
}