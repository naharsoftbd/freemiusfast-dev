import { ImgHTMLAttributes } from 'react';

export default function ApplicationLogo(
    props: ImgHTMLAttributes<HTMLImageElement>
) {
    return (
        <img
            src="/default/logo.png"
            alt="Application Logo"
            {...props}
        />
    );
}
