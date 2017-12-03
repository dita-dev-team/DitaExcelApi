import * as axios from "axios";

function getPapers(page, filter) {
    let url = page === null ? '/api/v2/papers' : `/api/v2/papers?page=${page}`;
    if (filter !== null) {
        url += page === null ? `?filter=${filter}` : `&filter=${filter}`;
    }
    return axios.get(url);
}

export {
    getPapers
}