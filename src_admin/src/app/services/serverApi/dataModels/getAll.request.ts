export class GetAllRequest {
    sorting: string;
    filter: any;
    constructor(sorting: string, filter: any) {
        this.sorting = sorting;
        this.filter = filter;
    }
}