import { SiteEntity } from './../../../../../entities/index';
import { IValidationService } from './../../../i-validation-service';
export interface ISiteValidationService extends IValidationService<SiteEntity> {
    isNameValid(site: SiteEntity): boolean;
    isOwnerValid(site: SiteEntity): boolean;
    isUrlValid(site: SiteEntity): boolean;
    isImageUrlValid(entity: SiteEntity): boolean;
    isWASPUrlValid(site: SiteEntity): boolean;
    isZipCodeValid(site: SiteEntity): boolean;
    isAvailableTestDriveFromHomeValid(site: SiteEntity): boolean;
    isMaxDeliveryDistanceValid(site: SiteEntity): boolean;
    getInvalidNameMessageKey(site: SiteEntity): string;
    getInvalidOwnerMessageKey(site: SiteEntity): string;
    getInvalidUrlMessageKey(site: SiteEntity): string;
    getInvalidWASPUrlMessageKey(site: SiteEntity): string;
    getInvalidZipCodeMessageKey(site: SiteEntity): string;
    getInvalidAvailableTestDriveFromHomeMessageKey(site: SiteEntity): string;
    getInvalidMaxDeliveryDistanceMessageKey(site: SiteEntity): string;
}