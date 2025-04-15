
export function isEmailValid(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

export function isPasswordStrong(password) {

    const regex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
    return regex.test(password);
}

export function isNotEmpty(str) {
    return str && str.trim().length > 0;
}
