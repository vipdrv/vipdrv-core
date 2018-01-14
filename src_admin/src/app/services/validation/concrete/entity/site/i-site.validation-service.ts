import { SiteEntity } from './../../../../../entities/index';
import { IValidationService } from './../../../i-validation-service';
export interface ISiteValidationService extends IValidationService<SiteEntity> {
    isNameValid(site: SiteEntity): boolean;
    isOwnerValid(site: SiteEntity): boolean;
    isUrlValid(site: SiteEntity): boolean;
    isImageUrlValid(entity: SiteEntity): boolean;
    getInvalidNameMessageKey(site: SiteEntity): string;
    getInvalidOwnerMessageKey(site: SiteEntity): string;
    getInvalidUrlMessageKey(site: SiteEntity): string;
}