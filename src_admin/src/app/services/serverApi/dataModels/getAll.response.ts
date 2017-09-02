export class GetAllResponse<TEntity> {
    totalCount: number;
    items: Array<TEntity>;
    constructor(totalCount: number, items: Array<TEntity>) {
        this.totalCount = totalCount;
        this.items = items;
    }
}