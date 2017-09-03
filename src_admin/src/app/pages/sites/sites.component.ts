import { Component, OnInit } from '@angular/core';
import { SiteEntity } from './../../entities/index';
import { ISiteApiService, SiteApiService, GetAllResponse } from './../../services/serverApi/index';
@Component({
    selector: 'sites',
    styleUrls: ['./sites.scss'],
    templateUrl: './sites.html',
})
export class SitesComponent implements OnInit{
    /// fields
    protected totalCount: number;
    protected items: Array<SiteEntity>;
    /// injected dependencies
    protected siteApiService: ISiteApiService;
    /// ctor
    constructor(siteApiService: SiteApiService) {
        this.siteApiService = siteApiService;
    }
    /// methods
    ngOnInit(): void {
        this.loadData();
    }
    /// helpers
    protected loadData(): Promise<void> {
        let self = this;
        let loadItemsPromise = self.siteApiService
            .getAll(0, 25, 'name asc', null)
            .then(function (response: GetAllResponse<SiteEntity>): void {
                self.totalCount = response.totalCount;
                self.items = response.items;
            });
        return loadItemsPromise;
    }
}
