<template>
    <div class="container">
        <div>
            <h1>Past Papers</h1>
        </div>
        <div v-if="isFailed">
            <h3>
                {{ uploadError }}
            </h3>
        </div>
        <div v-if="isFetching">
            <p>
                Fetching data.....
            </p>
        </div>
        <div>
            <table class="table table-bordered table-hover table-striped table-responsive">
                <thead>
                <tr>
                    <th class="text-center" @click="ascending = !ascending">Name
                        <icon name="glyphicon-triangle-top"></icon>
                    </th>
                    <th class="text-center">Type</th>
                    <th class="text-center">Semester</th>
                    <th class="text-center">File</th>
                </tr>
                </thead>

                <tbody>
                <tr v-for="paper in ascending ? sortAscending : sortDescending">
                    <td class="text-center">{{ paper.name }}</td>
                    <td class="text-center">{{ paper.resource_type.toUpperCase() }}</td>
                    <td class="text-center">{{ paper.semester }}</td>
                    <td class="text-center"><a v-bind:href="'/file/name/' + paper.file">Download</a></td>
                </tr>
                </tbody>

            </table>
        </div>
        <div class="pagination-section text-center">
            <ul class="pagination">
                <li v-bind:class="{disabled: currentPage == 1 || totalPages == 0 }" @click="setPage(1)"><a>First</a>
                </li>
                <li v-for="page in totalPages" v-bind:class="{active: page == currentPage}" @click="setPage(page)">
                    <a>{{ page }}</a></li>
                <li v-bind:class="{disabled: currentPage == totalPages || totalPages == 0}"
                    @click="setPage(totalPages)"><a>Last</a></li>
            </ul>
        </div>
    </div>
</template>

<script>
    import {getPapers} from '../get-papers.service';

    const STATUS_INITIAL = 0, STATUS_FETCHING = 1, STATUS_SUCCESS = 2, STATUS_FAILED = 3;
    let currentPage = 1;
    export default {
        components: {
            "icon": require("vue-icons")
        },
        name: 'app',
        data() {
            return {
                uploadError: null,
                currentStatus: null,
                currentPage: null,
                totalPages: null,
                papers: [],
                ascending: true
            }
        },
        computed: {
            isInitial() {
                return this.currentStatus === STATUS_INITIAL;
            },
            isFetching() {
                return this.currentStatus === STATUS_FETCHING;
            },
            isSuccess() {
                return this.currentStatus === STATUS_SUCCESS;
            },
            isFailed() {
                return this.currentStatus === STATUS_FAILED;
            },
            sortAscending() {
                return this.papers.sort(function (a, b) {
                    if (a.name < b.name) {
                        return -1;
                    } else if (a.name > b.name) {
                        return 1;
                    } else {
                        return 0;
                    }
                })
            },
            sortDescending() {
                return this.papers.sort(function (a, b) {
                    if (a.name > b.name) {
                        return -1;
                    } else if (a.name < b.name) {
                        return 1;
                    } else {
                        return 0;
                    }
                })
            }
        },
        methods: {
            fetchPapers() {
                this.currentStatus = STATUS_FETCHING;
                getPapers(this.currentPage).then(x => {
                    this.currentStatus = STATUS_SUCCESS;
                    this.currentPage = x.data['current_page'];
                    this.totalPages = x.data['last_page'];
                    this.papers = x.data['data'];
                    $("html, body").animate({scrollTop: 0}, "slow");
                }).catch(err => {
                    this.uploadError = 'Oops! An error occurred.';
                    this.currentStatus = STATUS_FAILED;
                });
            },
            setPage(page) {
                this.currentPage = page;
                this.fetchPapers();
            },
        },
        mounted() {
            this.fetchPapers()
        }
    }

</script>

<style lang="scss">
    table th,
    table td {
        padding: 10px;
    }
</style>