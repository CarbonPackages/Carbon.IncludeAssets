import babel from "@rollup/plugin-babel";
import { terser } from "rollup-plugin-terser";

export default [
    {
        input: "Resources/Private/Assets/LoadCssAsync.js",
        plugins: [
            babel({
                babelHelpers: "bundled",
            }),
            terser({
                output: {
                    comments: false,
                },
            }),
        ],
        output: {
            sourcemap: false,
            file: "Resources/Private/Templates/LoadCssAsync.js",
            format: "iife",
        },
    },
];
