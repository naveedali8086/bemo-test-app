export function getParamValFromUrl(param) {
    const url_params = new URLSearchParams(window.location.search);
    return url_params.has(param) ? url_params.get(param) : '';
}
