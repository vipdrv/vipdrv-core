import { Component, OnInit } from '@angular/core';
import { SiteEntity } from './../../../entities/index';
import { ISiteApiService, SiteApiService, GetAllResponse } from './../../../services/serverApi/index';
@Component({
    selector: 'sites-table',
    styleUrls: ['./sitesTable.scss'],
    templateUrl: './sitesTable.html',
})
export class SitesTableComponent implements OnInit {
    /// fields
    protected initialized: boolean;
    protected totalCount: number;
    protected items: Array<SiteEntity>;
    /// injected dependencies
    protected siteApiService: ISiteApiService;
    /// ctor
    constructor(siteApiService: SiteApiService) {
        this.siteApiService = siteApiService;
        this.initialized = false;
    }
    /// methods
    ngOnInit(): void {
        let self = this;
        self.getAllEntities()
            .then(() => self.initialized = true);
    }
    protected getAllEntities(): Promise<void> {
        let self = this;
        let operationPromise = self.siteApiService
            .getAll(0, 25, 'name asc', null)
            .then(function (response: GetAllResponse<SiteEntity>): Promise<void> {
                self.totalCount = response.totalCount;
                self.items = response.items;
                return Promise.resolve();
            });
        return operationPromise;
    }
    protected editEntity(id: number): void {

    }
    protected deleteEntity(id: number): Promise<void> {
        let self = this;
        let operationPromise = self.siteApiService
            .delete(id)
            .then(function (): Promise<void> {
                return self.getAllEntities();
            });
        return operationPromise;
    }
}
