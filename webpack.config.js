const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
	mode: 'development',
	devtool: 'inline-source-map',
	watch: true,
	plugins: [new MiniCssExtractPlugin({
		filename: 'Resources/Public/Css/dist/[name].css',
	})],
	entry: {
		App: './Resources/Private/TypeScript/App.ts'
	},
	output: {
		filename: 'Resources/Public/JavaScript/dist/[name].js',
		path: path.resolve(__dirname, '.'),
	},
	module: {
		rules: [
			{
				test: /\.tsx?$/,
				use: 'ts-loader'
			},
			{
				test: /\.(eot|gif|otf|png|svg|ttf|woff|woff2)(\?v=[0-9]\.[0-9]\.[0-9])?$/,
				loader: 'file-loader',
				options: {
					outputPath: 'Resources/Public/Images/dist',
					publicPath: '../../Images/dist'
				},
			},
			{
				test: /\.css$/i,
				use: [MiniCssExtractPlugin.loader, 'css-loader'],
			},
			{
				test: /\.s[ac]ss$/i,
				use: [
					MiniCssExtractPlugin.loader,
					"css-loader",
					"sass-loader",
				],
			},
		],
	},
	resolve: {
		extensions: ['.tsx', '.ts', '.js'],
	}
};
