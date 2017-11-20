import { IEntity, ILightEntity } from './../../entities/index';
import { ICRUDApiService } from './i-crud.api-service';
/// is used to communicate with server's sites controller (extended with specific methods)
export interface ICRUDXApiService<TEntity extends IEntity<TKey>, TKey, TLightEntity extends ILightEntity<TKey>>
    extends ICRUDApiService<TEntity, TKey, TLightEntity> {
    patchActivity(id: TKey, value: boolean): Promise<void>;
    patchOrder(id: TKey, value: number): Promise<void>;
}
