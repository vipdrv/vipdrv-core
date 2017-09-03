import { IEntity, ILightEntity } from './../../entities/index';
import { ICRUDApiService } from './i-crud.api-service';
import { IApiService } from './i-api-service';
import { GetAllResponse } from './dataModels/getAll.response';
/// is used to communicate with server's sites controller
export interface ICRUDApiService<TEntity extends IEntity<TKey>, TKey, TLightEntity extends ILightEntity<TKey>>
    extends IApiService {
    getAll(page: number, pageSize: number, sorting, filter: any): Promise<GetAllResponse<TEntity>>;
    getAllLight(page: number, pageSize: number, sorting: string, filter: any): Promise<GetAllResponse<TLightEntity>>;
    get(id: TKey): Promise<TEntity>;
    create(id: TEntity): Promise<TEntity>;
    update(id: TEntity): Promise<TEntity>;
    delete(id: TKey): Promise<void>;
}
