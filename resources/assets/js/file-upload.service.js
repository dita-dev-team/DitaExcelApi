import * as axios from "axios";

function upload(formData) {
    const url = '/api/v1/files/db';
    return axios.post(url, formData);
}

function uploadPastPaper(formData) {
    const url = '/api/v2/papers';
    return axios.post(url, formData);
}

export {upload, uploadPastPaper}