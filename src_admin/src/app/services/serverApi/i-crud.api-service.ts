import { IApiService } from "./../i-api-service";
import { IEntity } from "./../../entities/i-entity";
import { GetAllRequest } from "./../dataModels/getAll.request";
import { GetAllResponse } from "./../dataModels/getAll.response";
/// is used to communicate with server's site controller
export interface ICRUDApiService<TEntity extends IEntity<TKey>, TKey> extends IApiService {
    get(id: TKey): Promise<TEntity>;
    create(id: TEntity): Promise<TEntity>;
    update(id: TEntity): Promise<TEntity>;
    delete(id: TKey): Promise<void>;
}