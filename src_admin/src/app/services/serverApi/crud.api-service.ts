import { Injectable } from '@angular/core';
import { IHttpService, ILogger, Variable } from './../../utils/index';
import { IEntity, ILightEntity } from './../../entities/index';
import { ICRUDApiService } from './i-crud.api-service';
import { BaseApiService } from './base.api-service';
import { UrlParameter } from './urlParameter';
import { GetAllResponse } from './dataModels/getAll.response';
@Injectable()
export abstract class CRUDApiService<TEntity extends IEntity<TKey>, TKey, TLightEntity extends ILightEntity<TKey>>
    extends BaseApiService
    implements ICRUDApiService<TEntity, TKey, TLightEntity> {
    /// ctor
    constructor(
        httpService: IHttpService,
        logger: ILogger,
        controllerName: string) {
        super(httpService, logger, controllerName);
    }
    /// methods
    getAll(page: number, pageSize: number, sorting: string, filter: any): Promise<GetAllResponse<TEntity>> {
        return this.innerGetMany<TEntity>(
            'get-all', this.createEmptyEntity,
            page, pageSize, sorting, filter);
    }
    getAllLight(page: number, pageSize: number, sorting: string, filter: any): Promise<GetAllResponse<TLightEntity>> {
        return this.innerGetMany<TLightEntity>(
            'get-all', this.createEmptyLightEntity,
            page, pageSize, sorting, filter);
    }
    get(id: TKey): Promise<TEntity> {
        let self = this;
        return this.httpService
            .get(this.createUrlWithMethodNameAndParams('', String(id)))
            .then(function (response: any): TEntity {
                let entity: TEntity = self.createEmptyEntity();
                entity.initializeFromDto(response);
                return entity;
            });
    }
    create(entity: TEntity): Promise<TEntity> {
        let self = this;
        return this.httpService
            .post(this.createUrlWithMethodName(''), entity)
            .then(function (response: any): TEntity {
                let entity: TEntity = self.createEmptyEntity();
                entity.initializeFromDto(response);
                return entity;
            });
    }
    update(entity: TEntity): Promise<TEntity> {
        let self = this;
        return this.httpService
            .put(this.createUrlWithMethodName(''), entity)
            .then(function (response: any): TEntity {
                let entity: TEntity = self.createEmptyEntity();
                entity.initializeFromDto(response);
                return entity;
            });
    }
    delete(id: TKey): Promise<void> {
        return this.httpService
            .delete(this.createUrlWithMethodNameAndParams('', String(id)))
            .then(function (response: any): void { });
    }
    ///helpers
    protected innerGetMany<T extends IEntity<TKey>>(
        methodName: string, createEmptyT: () => T,
        page: number, pageSize: number, sorting, filter: any): Promise<GetAllResponse<T>> {
        let pageParameter = new UrlParameter('page', page);
        let pageSizeParameter = new UrlParameter('pageSize', pageSize);
        let request = Variable.isNotNullOrUndefined(filter) ? filter : {};
        request.sorting = sorting;
        return this.httpService
            .post(this.createUrlWithMethodNameAndUrlParams(methodName, pageParameter, pageSizeParameter), request)
            .then(function (response: any): GetAllResponse<T> {
                if (Variable.isNullOrUndefined(response)) {
                    throw new Error('GetMany method returns not defined response.');
                }
                if (Variable.isNullOrUndefined(response.items)) {
                    throw new Error('GetMany method returns response with not defined items.');
                }
                let entities: Array<T> = new Array<T>();
                response.items.forEach(function (item: any): void {
                    let entity: T = createEmptyT();
                    entity.initializeFromDto(item);
                    entities.push(entity);
                });
                return new GetAllResponse<T>(response.totalCount, entities);
            });
    }
    protected abstract createEmptyEntity(): TEntity;
    protected abstract createEmptyLightEntity(): TLightEntity;
}