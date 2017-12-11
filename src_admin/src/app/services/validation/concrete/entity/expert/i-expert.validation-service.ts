import { ExpertEntity } from './../../../../../entities/index';
import { IValidationService } from './../../../i-validation-service';
export interface IExpertValidationService extends IValidationService<ExpertEntity> {
    isValidName(entity: ExpertEntity): boolean;
    isValidDescription(entity: ExpertEntity): boolean;
    isValidLinkedInUrl(entity: ExpertEntity): boolean;
    isValidFacebookUrl(entity: ExpertEntity): boolean;
    getInvalidNameMessageKey(entity: ExpertEntity): string;
    getInvalidDescriptionMessageKey(entity: ExpertEntity): string;
    getInvalidLinkedInUrlMessageKey(entity: ExpertEntity): string;
    getInvalidFacebookUrlMessageKey(entity: ExpertEntity): string;
}