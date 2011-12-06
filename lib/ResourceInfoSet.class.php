<?php


/**
 * A wrapper of ResourceInfo objects. Much like the ClassInfoSet object.
 *
 * @author Michael Irwin
 */
class ResourceInfoSet {
  
  /**
   *
   * @var ResourceInfo[]
   */
  private $resourceInfoSet;
  
  /**
   * Default constructor.  Initializes the resourceInfoSet.
   */
  public function ResourceInfoSet() {
    $this->resourceInfoSet = array();
  }
  
  /**
   * Add a resource to the set of resources being managed.
   * @param ResourceInfo $resource The resource to add.
   */
  public function addResource(ResourceInfo $resource) {
    $this->resourceInfoSet[$resource->getReference()] = $resource;
  }
  
  /**
   * Get a specific resource by its reference.
   * @param string $resourceName The reference for the resource.
   * @return ResourceInfo
   */
  public function getResource($resourceName) {
    if (isset($this->resourceInfoSet[$resourceName]))
      return $this->resourceInfoSet[$resourceName];
    return null;
  }
  
  /**
   * Given either a resource name, find the reference.
   * @param type $resourceName
   * @return string The reference to the resource.
   */
  public function findResource($resourceName) {
    $temp = $this->getResource($resourceName);
    if ($temp != null) return $resourceName;
    
    foreach ($this->resourceInfoSet as $reference => $data) {
      if ($data->getResourceName() == $resourceName) {
        return $reference;
      }
    }
    return null;
  }
  
}

