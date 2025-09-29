/** @type {import('tailwindcss').Config} */
export default {
    content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}'],
    theme: {
        extend: {
            colors: {
                blue: {
                    DEFAULT: '#34495E',
                    light: '#0156A3',
                    dark: '#2C3D50',
                },
                gray: {
                    DEFAULT: '#D9D9D9',
                    dark: '#A9A9AC',
                    light: '#F5F5F5',
                },
                black: {
                    DEFAULT: '#4B4B4B',
                },
                red: {
                    DEFAULT: '#E1001D',
                    error: '#F44336',
                },
            },
            boxShadow: {
                default: '0 0.25rem 0.25rem 0 rgba(0, 0, 0, 0.25)',
                card: '0 0.25rem 0.25rem 0rem rgba(0, 0, 0, 0.5)',
            },

            margin: {
                '4.5': '1.125rem',
            },

            height: {
                '7.5': '1.875rem',
            },
            width: {
                '72.5': '18.125rem',
            },
            fontFamily: {
                arial: ['Arial', 'sans-serif'],
                roboto: ['Roboto', 'sans-serif'],
                times: ['"Times New Roman"', 'sans-serif'],
            },
        },
    },
    plugins: [],
};
