export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                primary: '#EAB308', // Yellow-500
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
                serif: ['"Playfair Display"', 'serif'],
                customHeader: ['"wf_a339f259334e44ff9a746f30d"', 'sans-serif'],
                customBody: ['"madefor-display"', 'sans-serif'],
            },
        },
    },
    plugins: [],
};
