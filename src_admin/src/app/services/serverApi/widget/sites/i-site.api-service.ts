import { SiteEntity, LightEntity } from '../../../../entities/index';
import { ICRUDApiService } from '../../i-crud.api-service';
/// is used to communicate with server's sites controller
export interface ISiteApiService extends ICRUDApiService<SiteEntity, number, LightEntity> {
    patchContacts(id: number, value: any): Promise<void>;
    patchUseExpertStep(id: number, value: boolean): Promise<void>;
    patchUseBeverageStep(id: number, value: boolean): Promise<void>;
    patchUseRouteStep(id: number, value: boolean): Promise<void>;
    swapBeverageExpertStepOrder(siteId: number): Promise<void>;
    swapBeverageRouteStepOrder(siteId: number): Promise<void>;
    swapExpertRouteStepOrder(siteId: number): Promise<void>;
}
