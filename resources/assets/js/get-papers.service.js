import * as axios from "axios";

function getPapers(page) {
    const url = page === null ? '/api/v2/papers' : `/api/v2/papers?page=${page}`;
    return axios.get(url);
}

export {
    getPapers
}