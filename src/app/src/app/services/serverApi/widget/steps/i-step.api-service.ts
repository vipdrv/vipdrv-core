import { StepEntity, LightEntity } from '../../../../entities/index';
import { ICRUDXApiService } from '../../i-crudx.api-service';
/// is used to communicate with server's steps controller
export interface IStepApiService extends ICRUDXApiService<StepEntity, number, LightEntity> {
}