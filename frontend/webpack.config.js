const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const Dotenv = require('dotenv-webpack');

module.exports = {
  entry: './src/index.ts', // Ensure this points to the correct file
  output: {
    filename: 'main.js',
    path: path.resolve(__dirname, 'dist'),
  },
  mode: 'development', // Set mode to development or production
  plugins: [
    new HtmlWebpackPlugin({
      template: path.resolve(__dirname, 'public', 'index.html'), // Path to your HTML file
      filename: 'index.html', // Output HTML file name
    }),
    new Dotenv({
        path: './.env.local',  // Load .env.local file
    }),
  ],
  resolve: {
    extensions: ['.ts', '.js'], // Resolve TypeScript and JavaScript files
  },
  module: {
    rules: [
      {
        test: /\.ts$/, // Apply loader to .ts files
        use: 'ts-loader',
        exclude: /node_modules/, // Exclude node_modules
      },
    ],
  },
  devServer: {
    static: {
        directory: path.resolve(__dirname, 'dist'), // Serve files from dist
      },
    compress: true,
    port: 9000, // Change to the port you want
  },
};
